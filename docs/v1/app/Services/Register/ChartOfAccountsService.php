<?php

namespace App\Services\Register;

use App\Exceptions\ValidationException;
use App\Models\Register\ChartOfAccounts;
use App\Models\Register\ChartOfAccountsGroup;
use App\Services\Service;
use Illuminate\Support\Facades\Validator;

class ChartOfAccountsService extends Service 
{
    public function index($id)
    {
        $ChartOfAccounts = ChartOfAccounts::where([['group_id', $id]])->get();
        return $ChartOfAccounts;
    }

    public function show($id)
    {
        $ChartOfAccounts = ChartOfAccounts::findOrFail($id);
        return $ChartOfAccounts;
    }
    public function getGroup($id)
    {
        $ChartOfAccounts = ChartOfAccountsGroup::findOrFail($id);
        return $ChartOfAccounts;
    }
    public function store(Array $data)
    {
        $this->validate($data);
            $ChartOfAccounts = new ChartOfAccounts();
            $codeConvert = str_replace("-", "", $data['code']);
            $ChartOfAccounts->code = $codeConvert;
            $ChartOfAccounts->name  = $data['name'];
            $ChartOfAccounts->group_id = $data['group_id'];
            $ChartOfAccounts->save();
            return $ChartOfAccounts;
    }

    public function update(Array $data): ChartOfAccounts
    {
        $this->validate($data); 
        $ChartOfAccounts = $this->show($data['id']);
        $codeConvert = str_replace("-", "", $data['code']);
        $ChartOfAccounts->code = $codeConvert ?? $ChartOfAccounts->code;
        $ChartOfAccounts->name = $data['name'] ?? $ChartOfAccounts->name;
		$ChartOfAccounts->save();
        return $ChartOfAccounts;
    }

    public function checkCode(array $data)
    {
        $codeFormat = str_replace("-", "", $data['code']);
        $code = ChartOfAccounts::where([['code', $codeFormat]])->first();
        if ($code == null) {
            return false;
        }else{
            return true;
        }
    }

    public function destroy($id)
    {
        $ChartOfAccounts = $this->show($id);
        $ChartOfAccounts->delete();
        return $ChartOfAccounts;
    }

    private function validate(Array $data): bool
    {
        $validator = Validator::make(
            $data, 
            [
                'code' => 'required',
                'name' => 'required|max:255', 
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