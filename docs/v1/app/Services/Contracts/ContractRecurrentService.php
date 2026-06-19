<?php

namespace App\Services\Contracts;

use App\Exceptions\ValidationException;
use App\Models\Contract\ContractRecurrent;
use App\Models\Contract\ContractRecurrentParcelings;
use App\Models\Contract\ContractsFiles;
use App\Models\CostCenter;
use App\Models\Register\ChartOfAccounts;
use App\Models\Register\PaymentMethods;
use App\Models\Register\Supplier;
use App\Services\Service;
use DateTime;
use Illuminate\Support\Facades\Validator;

class ContractRecurrentService extends Service
{

    const STATUS = ['Previsto' => 0, 'Compromissado' => 1, 'Não Pago' => 2, 'Pago' => 3];

    public function index()
    {
        $contractRecurrent = ContractRecurrent::all();
        return $contractRecurrent;
    }

    public function getContractRecurrentParcelings($id)
    {
        $contractRecurrentParcelings = ContractRecurrentParcelings::where([['contract_recurrents_id', $id], ['status_id', 0]])->get();
        return $contractRecurrentParcelings;
    }

    //GET
    public function getSupllier(int $id = null)
    {
        if (isset($id)) {
            $supllier = Supplier::findOrFail($id);
        } else {
            $supllier = Supplier::all();
        }
        return $supllier;
    }
    public function getCostCenters(int $id = null)
    {
        if (isset($id)) {
            $cost_centers = CostCenter::findOrFail($id);
        } else {
            $cost_centers = CostCenter::all();
        }
        return $cost_centers;
    }
    public function getChartOfAccount(int $id = null)
    {
        if (isset($id)) {
            $chart_of_accounts = ChartOfAccounts::findOrFail($id);
        } else {
            $chart_of_accounts = ChartOfAccounts::all();
        }
        return $chart_of_accounts;
    }
    public function getPaymentMethods(int $id = null)
    {
        if (isset($id)) {
            $payment_methods = PaymentMethods::findOrFail($id);
        } else {
            $payment_methods = PaymentMethods::all();
        }
        return $payment_methods;
    }

    public function getInstallments($id = null)
    {
        if (isset($id)) {
            $contractRecurrentParcelings = ContractRecurrentParcelings::findOrFail($id);
        } else {
            $contractRecurrentParcelings = ContractRecurrentParcelings::all();
        }
        return $contractRecurrentParcelings;
    }

    public function getInstallmentsContract($id, $status = null)
    {
        if (isset($status)) {
            $contractRecurrentParcelings = ContractRecurrentParcelings::where([['contract_recurrents_id', $id], ['status_id', $status]])->get();
        } else {
            $contractRecurrentParcelings = ContractRecurrentParcelings::where([['contract_recurrents_id', $id]])->get();
        }
        return $contractRecurrentParcelings;
    }

    public function getContractFile($id)
    {
        $contractsFiles = ContractsFiles::findOrFail($id);
        return $contractsFiles;
    }

    public function show(int $id)
    {
        $contract_recurrent = ContractRecurrent::findOrFail($id);
        return $contract_recurrent;
    }

    public function store(array $data)
    {
        $this->validate($data);
        $contract_recurrent = new ContractRecurrent();
        $contract_recurrent->suppliers_id = $data['suppliers_id'];
        $contract_recurrent->cost_centers_id = $data['cost_centers_id'];
        $contract_recurrent->chart_of_accounts_id = $data['chart_of_accounts_id'];
        $contract_recurrent->start_period = $data['start_period'];
        $contract_recurrent->end_period = $data['end_period'];
        $contract_recurrent->payment_methods_id = $data['payment_methods_id'];
        $contract_recurrent->object = $data['object'];
        $contract_recurrent->price = $data['price'];
        $contract_recurrent->day_of_maturities = $data['day_of_maturities'];
        $contract_recurrent->readjustment_index = $data['readjustment_index'];
        $contract_recurrent->early_termination = $data['early_termination'];
        $contract_recurrent->early_termination_penalty = $data['early_termination_penalty'];
        $contract_recurrent->bank = $data['bank'];
        $contract_recurrent->agency = $data['agency'];
        $contract_recurrent->account = $data['account'];
        $contract_recurrent->observation = $data['observation'];
        $contract_recurrent->save();
        $this->generateParcelings($contract_recurrent->id, $contract_recurrent->start_period, $contract_recurrent->end_period, $contract_recurrent->price, $contract_recurrent->day_of_maturities, $contract_recurrent->payment_methods_id);
        return $contract_recurrent;
    }
    /**
     * Undocumented function
     *
     * @param [type] $contract_recurrent_id
     * @param [type] $start_period
     * @param [type] $end_period
     * @param [type] $monthly_value
     * @param [type] $day_of_maturities
     * @return bool
     */
    public function generateParcelings($contract_recurrent_id, $start_period, $end_period, $monthly_value, $day_of_maturities, $payment_methods_id)
    {
        $start_period = new DateTime($start_period);
        $end_period = new DateTime($end_period);
        $current_date = new DateTime();
        $interval = $end_period->diff($start_period);

        $due_date = $start_period->format($day_of_maturities . '-m-Y');
        $due_date = new DateTime($due_date);

        for ($i = 0; $i < $interval->m; $i++) {
            $due_date = $due_date->format($day_of_maturities . '-m-Y');
            $due_date = new DateTime($due_date);
            $data = [
                'contract_recurrents_id' => $contract_recurrent_id,
                'planned_date' => $due_date,
                'monthly_value' => $monthly_value,
                'status_id' => self::STATUS['Previsto'],
                'payment_methods_id' => $payment_methods_id,
                'created_at' => $current_date,
            ];
            $due_date->modify('first day of +1 month');
            $result = ContractRecurrentParcelings::insert($data);
        }
        return $result;
    }

    /**
     * Undocumented function
     *  name, contract_recurrents_id, file_url
     * @param array $data
     * @return void
     */
    public function files_store(array $data)
    {
        $path = $data['file']->store('files');
        $contractsFiles = new ContractsFiles();
        $contractsFiles->name = $data['name'];
        $contractsFiles->contract_recurrents_id = $data['contract_recurrents_id'];
        $contractsFiles->file_url = $path;
        $contractsFiles->save();
        return $contractsFiles;
    }

    public function list_files($id)
    {
        $contractsFiles = ContractsFiles::where([['contract_recurrents_id', $id]])->get();
        return $contractsFiles;
    }

    /**
     * Undocumented function
     * @return bool
     */
    public function createteParcelings(array $data)
    {
        $this->installments_validate($data);

        $planned_date = new DateTime($data['planned_date']);
        $day = $planned_date->format('d');
        $current_date = new DateTime();
        for ($i = 0; $i < $data['expected_month_amount']; $i++) {

            $planned_date = $planned_date->format($day . '-m-Y');
            $planned_date = new DateTime($planned_date);
            $insert = [
                'contract_recurrents_id' => $data['contract_recurrents_id'],
                'planned_date' => $planned_date->format('Y-m-d'),
                'monthly_value' => $data['monthly_value'],
                'payment_methods_id' => $data['payment_methods_id'],
                'status_id' => self::STATUS['Previsto'],
                'created_at' => $current_date,
            ];

            $planned_date->modify('first day of +1 month');

            $result = ContractRecurrentParcelings::insert($insert);
        }
        return $result;
    }

    public function previewParcelings(int $amountParcelings, $planned_date)
    {
        $planned_date = new DateTime($planned_date);
        $day = $planned_date->format('d');

        for ($i = 0; $i < $amountParcelings; $i++) {
            $planned_date = $planned_date->format($day . '-m-Y');
            $planned_date = new DateTime($planned_date);
            $result[] = $planned_date->format('d/m/Y');
            $planned_date->modify('first day of +1 month');
        }

        if (!empty($result)) {
            return $result;
        }
        return false;
    }

    public function update(array $data)
    {
        $this->validate($data);
        $contract_recurrent = $this->show($data['id']);
        $contract_recurrent->suppliers_id = $data['suppliers_id'] ?? $contract_recurrent->suppliers_id;
        $contract_recurrent->cost_centers_id = $data['cost_centers_id'] ?? $contract_recurrent->cost_centers_id;
        $contract_recurrent->chart_of_accounts_id = $data['chart_of_accounts_id'] ?? $contract_recurrent->chart_of_accounts_id;
        $contract_recurrent->start_period = $data['start_period'] ?? $contract_recurrent->start_period;
        $contract_recurrent->end_period = $data['end_period'] ?? $contract_recurrent->end_period;
        $contract_recurrent->payment_methods_id = $data['payment_methods_id'] ?? $contract_recurrent->payment_methods_id;
        $contract_recurrent->object = $data['object'] ?? $contract_recurrent->object;
        $contract_recurrent->price = $data['price'] ?? $contract_recurrent->price;
        $contract_recurrent->day_of_maturities = $data['day_of_maturities'] ?? $contract_recurrent->day_of_maturities;
        $contract_recurrent->readjustment_index = $data['readjustment_index'] ?? $contract_recurrent->readjustment_index;
        $contract_recurrent->early_termination = $data['early_termination'] ?? $contract_recurrent->early_termination;
        $contract_recurrent->early_termination_penalty = $data['early_termination_penalty'] ?? $contract_recurrent->early_termination_penalty;
        $contract_recurrent->bank = $data['bank'] ?? $contract_recurrent->bank;
        $contract_recurrent->agency = $data['agency'] ?? $contract_recurrent->agency;
        $contract_recurrent->account = $data['account'] ?? $contract_recurrent->account;
        $contract_recurrent->observation = $data['observation'] ?? $contract_recurrent->observation;
        $contract_recurrent->save();
        return $contract_recurrent;
    }

    public function installments_update(array $data)
    {
        $this->installments_validate($data);
        $contractRecurrentParcelings = ContractRecurrentParcelings::find($data['id']);
        $contractRecurrentParcelings->planned_date = $data['planned_date'] ?? $contractRecurrentParcelings->planned_date;
        $contractRecurrentParcelings->monthly_value = $data['monthly_value'] ?? $contractRecurrentParcelings->monthly_value;
        $contractRecurrentParcelings->payment_methods_id = $data['payment_methods_id'] ?? $contractRecurrentParcelings->payment_methods_id;
        $contractRecurrentParcelings->save();
        return $contractRecurrentParcelings;
    }

    public function installments_commitment(array $data)
    {
        $path = $data['file']->store('files');
        $contractRecurrentParcelings = ContractRecurrentParcelings::find($data['id']);
        $contractRecurrentParcelings->fiscal_note = $data['fiscal_note'] ?? $contractRecurrentParcelings->fiscal_note;
        $contractRecurrentParcelings->emission_date = $data['emission_date'] ?? $contractRecurrentParcelings->emission_date;
        $contractRecurrentParcelings->payment_methods_id = $data['payment_methods_id'] ?? $contractRecurrentParcelings->payment_methods_id;
        $contractRecurrentParcelings->due_date = $data['due_date'] ?? $contractRecurrentParcelings->due_date;
        $contractRecurrentParcelings->commitment_value = $data['commitment_value'] ?? $contractRecurrentParcelings->commitment_value;
        $contractRecurrentParcelings->file_url = $path ?? $contractRecurrentParcelings->file_url;
        $contractRecurrentParcelings->status_id = self::STATUS['Compromissado'] ?? $contractRecurrentParcelings->status_id;
        $contractRecurrentParcelings->save();
        return $contractRecurrentParcelings;
    }

    public function installments_destroy($id)
    {
        $contractRecurrentParcelings = $this->getInstallments($id);
        $contractRecurrentParcelings->delete();
        return $contractRecurrentParcelings;
    }
    public function destroy($id)
    {
        $contract_recurrent = $this->show($id);
        $contract_recurrent->delete();
        return $contract_recurrent;
    }

    private function validate(array $data): bool
    {
        $validator = Validator::make(
            $data,
            [
                'suppliers_id' => 'required',
                'cost_centers_id' => 'required',
                'chart_of_accounts_id' => 'required',
                'start_period' => 'required',
                'end_period' => 'required',
                'payment_methods_id' => 'required',
                'price' => 'required',
                'day_of_maturities' => 'required',
                'readjustment_index' => 'required',
                'early_termination' => 'required',
                'bank' => 'required',
                'agency' => 'required',
                'account' => 'required',
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

    private function installments_validate(array $data): bool
    {
        $validator = Validator::make(
            $data,
            [
                'contract_recurrents_id' => 'required',
                'planned_date' => 'required',
                'monthly_value' => 'required',
                'payment_methods_id' => 'required',
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
