<?php

namespace App\Services\Register;

use App\Exceptions\ValidationException;
use App\Models\CostCenter;
use App\Services\Service;
use Illuminate\Support\Facades\Validator;


class CostCenterService extends Service  
{

    public function index()
    {
        $costCenter = CostCenter::All();
        return $costCenter;
    }

    public function show(int $id) 
    {
        return CostCenter::findOrFail($id);
    }

    public function store(Array $data)
    {
           $this->validate($data);
            $costCenter = new CostCenter();
            $codeFormat =  str_replace("-", "", $data['code']);
            $costCenter->code = $codeFormat;
            $costCenter->name = $data['name'];
            $costCenter->save();
            return $costCenter;
    }
    
    public function update(Array $data)
    {
        $this->validate($data); 
        
        $costCenter = $this->show($data['id']);
        $codeFormat =  str_replace("-", "", $data['code']);
        $costCenter->code = $codeFormat ?? $costCenter->code;
        $costCenter->name = $data['name'] ?? $costCenter->name;
        $costCenter->save();

        return $costCenter;
    }

    public function checkCode(array $data)
    {
        $codeFormat = str_replace("-", "", $data['code']);
        $code = CostCenter::where([['code', $codeFormat]])->first();
        if ($code == null) {
            return false;
        }else{
            return true;
        }
    }

    public function destroy($id)
    {
        $costCenter = $this->show($id);
        $costCenter->delete();
        return $costCenter;
    }

    private function validate(Array $data): bool
    {
        $validator = Validator::make(
            $data, 
            [
                'code' => 'required',
                'name' => 'required',
            ],
            $this->getDefaultMessages()
        );

        if ($validator->fails()) {
            $e = new ValidationException('INVALID_DATA', 400);
            $e->setMessages($validator->errors()->getMessages());
            throw $e;
        }

        return $validator->fails();
    }
}