<?php

namespace App\Services\Register;

use App\Exceptions\ValidationException;
use App\Models\Register\Account;
use App\Services\Service;
use Illuminate\Support\Facades\Validator;

class AccountService extends Service 
{
    public function index()
    {
        $account = Account::all();
        return $account;
    }

    public function show($id)
    {
        $account = Account::findOrFail($id);
        return $account;
    }
    public function store(Array $data)
    {
        $this->validate($data);
            $account = new Account();
            $account->description = $data['description'];
            $account->balance  = $this->ConvertValor($data['balance']);
            $account->save();
            return $account;
    } 

    public function ConvertValor($valor) {
        $verificaPonto = ".";
        if(strpos("[".$valor."]", "$verificaPonto")):
            $valor = str_replace('.','', $valor);
            $valor = str_replace(',','.', $valor);
            else:
              $valor = str_replace(',','.', $valor);   
        endif;
 
        return $valor;
 }

    public function update(Array $data): Account
    {
        $this->validate($data); 
        $account = $this->show($data['id']);
        $account->description = $data['description'] ?? $account->description;
        $account->balance =  $data['balance'] ?? $account->balance;
		$account->save();
        return $account;
    }

    public function destroy($id)
    {
        $account = $this->show($id);
        $account->delete();
        return $account;
    }

    private function validate(Array $data): bool
    {
        $validator = Validator::make(
            $data, 
            [
                'description' => 'required',
                'balance' => 'required'
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