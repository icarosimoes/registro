<?php

namespace App\Services\Register;

use App\Exceptions\ValidationException;
use App\Models\Register\GroupProduct;
use App\Models\User;
use App\Services\Service;
use App\Services\ServiceInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;

class GroupProductService extends Service
{

    public function index()
    {
       $groupProduct = GroupProduct::all();
       return $groupProduct;
    }

    public function show(int $id): GroupProduct 
    {
        return GroupProduct::findOrFail($id);
    }

    public function checkCode(array $data)
    {
        $codeFormat = str_replace("-", "", $data['code']);
        $code = GroupProduct::where([['code', $codeFormat]])->first();
        if ($code == null) {
            return false;
        } else {
            return true;
        }
    }

    public function store(Array $data)
    {
        $this->validate($data);
        $groupProduct = new GroupProduct();
        $groupProduct->code = $data['code'];
        $groupProduct->description = $data['description'];
        $groupProduct->save();
        return $groupProduct;
    
    }

    public function update(Array $data): GroupProduct
    {
        $this->validate($data); 
        
        $groupProduct = $this->show($data['id']);
        $groupProduct->code = $data['code'];
        $groupProduct->description = $data['description'];
        $groupProduct->save();

        return $groupProduct;
    }

    public function destroy($id)
    {
        try {
            $groupProduct = $this->show($id);
            $groupProduct->delete();
            return $groupProduct;
        } catch (\Exception $e) {
            return redirect()->back()->with('alert', 'Desculpe, não é possível excluir o dado selecionado, Existem produtos ligados a esse registro!');
        }
        
    }

    private function validate(Array $data): bool
    {
        $validator = Validator::make(
            $data, 
            [
                'code' => 'required', 
				'description' => 'required', 
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