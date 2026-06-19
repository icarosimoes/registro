<?php

namespace App\Services\Register;

use App\Exceptions\ValidationException;
use App\Models\Register\Account;
use App\Models\Register\PaymentMethods;
use App\Services\Service;
use Illuminate\Support\Facades\Validator;

class PaymentMethodService extends Service 
{
    public function index()
    {
        $paymentMethods = PaymentMethods::all();
        return $paymentMethods;
    }

    public function show($id)
    {
        $paymentMethods = PaymentMethods::findOrFail($id);
        return $paymentMethods;
    }
    public function getAccount()
    {
        $account = Account::all();
        return $account;
    }
    public function store(Array $data)
    {
        $this->validate($data);
            $paymentMethods = new PaymentMethods();
            $paymentMethods->description = $data['description'];
            $paymentMethods->tax  = $data['tax'];
            $paymentMethods->deadline  = $data['deadline'];
            $paymentMethods->accounts_id  = $data['accounts_id'];
            $paymentMethods->save();
            return $paymentMethods;
    } 

    public function update(Array $data): PaymentMethods
    {
        $this->validate($data); 
        $paymentMethods = $this->show($data['id']);
        $paymentMethods->description = $data['description'] ?? $paymentMethods->description;
        $paymentMethods->tax = $data['tax'] ?? $paymentMethods->tax;
        $paymentMethods->deadline = $data['deadline'] ?? $paymentMethods->deadline;
        $paymentMethods->accounts_id = $data['accounts_id'] ?? $paymentMethods->accounts_id;
		$paymentMethods->save();
        return $paymentMethods;
    }

    public function destroy($id)
    {
        $paymentMethods = $this->show($id);
        $paymentMethods->delete();
        return $paymentMethods;
    }

    private function validate(Array $data): bool
    {
        $validator = Validator::make(
            $data, 
            [
                'description' => 'required',
                'tax' => 'required',
                'deadline' => 'required',
                'accounts_id' => 'required'
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