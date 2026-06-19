<?php

namespace App\Services\Register;

use App\Exceptions\ValidationException;
use App\Models\Register\ChartOfAccounts;
use App\Models\Register\ChartOfAccountsGroup;
use App\Services\Service;
use Illuminate\Support\Facades\Validator;

class ChartOfAccountsGroupService extends Service 
{
    public function index()
    {
        $ChartOfAccountsGroup = ChartOfAccountsGroup::all();
        return $ChartOfAccountsGroup;
    }

    public function show($id)
    {
        $ChartOfAccountsGroup = ChartOfAccountsGroup::findOrFail($id);
        return $ChartOfAccountsGroup;
    }
    public function store(Array $data)
    {
        $this->validate($data);
            $ChartOfAccountsGroup = new ChartOfAccountsGroup();
            $codeFormart = str_replace("-", "", $data['code']);
            $ChartOfAccountsGroup->code  = $codeFormart;
            $ChartOfAccountsGroup->name  = $data['name'];
            $ChartOfAccountsGroup->save();
            return $ChartOfAccountsGroup;
    }

    public function checkCode(array $data)
    {
        $codeFormat = str_replace("-", "", $data['code']);
        $code = ChartOfAccountsGroup::where([['code', $codeFormat]])->first();
        if ($code == null) {
            return false;
        }else{
            return true;
        }
    }

    public function update(Array $data): ChartOfAccountsGroup
    {
        $this->validate($data); 
        $ChartOfAccountsGroup = $this->show($data['id']);
        $codeFormart = str_replace("-", "", $data['code']);
        $ChartOfAccountsGroup->code = $codeFormart ?? $ChartOfAccountsGroup->code;
        $ChartOfAccountsGroup->name = $data['name'] ?? $ChartOfAccountsGroup->name;
		$ChartOfAccountsGroup->save();
        return $ChartOfAccountsGroup;
    }

    public function destroy($id)
    {
        $this->deleteChartOfAccounts($id); // delete cascade
        $ChartOfAccountsGroup = $this->show($id);
        $ChartOfAccountsGroup->delete();
        return $ChartOfAccountsGroup;
    }

    private function deleteChartOfAccounts($id)
    {
        $ChartOfAccounts = ChartOfAccounts::where([['group_id', $id]])->delete();
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