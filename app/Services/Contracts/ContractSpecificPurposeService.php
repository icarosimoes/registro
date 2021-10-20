<?php

namespace App\Services\Contracts;

use App\Exceptions\ValidationException;
use App\Models\Contract\ContractSpecificPurpose;
use App\Models\Contract\ContractSpecificPurposeParcelings;
use App\Models\Contract\ContractsFiles;
use App\Models\CostCenter;
use App\Models\Register\ChartOfAccounts;
use App\Models\Register\PaymentMethods;
use App\Models\Register\Supplier;
use App\Services\Service;
use DateTime;
use Illuminate\Support\Facades\Validator;

class ContractSpecificPurposeService extends Service
{

    const STATUS = ['Previsto' => 0, 'Compromissado' => 1, 'Não Pago' => 2, 'Pago' => 3];

    public function index()
    {
        $contractSpecificPurpose = ContractSpecificPurpose::all();
        return $contractSpecificPurpose;
    }

    public function getContractRecurrentParcelings($id)
    {
        $contractSpecificPurposeParcelings = ContractSpecificPurposeParcelings::where([['contract_specific_purposes_id', $id], ['status_id', 0]])->get();
        return $contractSpecificPurposeParcelings;
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
            $contractSpecificPurposeParcelings = ContractSpecificPurposeParcelings::findOrFail($id);
        } else {
            $contractSpecificPurposeParcelings = ContractSpecificPurposeParcelings::all();
        }
        return $contractSpecificPurposeParcelings;
    }

    public function getInstallmentsContract($id, $status = null)
    {
        if (isset($status)) {
            $contractSpecificPurposeParcelings = ContractSpecificPurposeParcelings::where([['contract_specific_purposes_id', $id], ['status_id', $status]])->get();
        } else {
            $contractSpecificPurposeParcelings = ContractSpecificPurposeParcelings::where([['contract_specific_purposes_id', $id]])->get();
        }
        return $contractSpecificPurposeParcelings;
    }

    public function getContractFile($id)
    {
        $contractsFiles = ContractsFiles::findOrFail($id);
        return $contractsFiles;
    }

    public function show(int $id)
    {
        $contract_specificPurpose = ContractSpecificPurpose::findOrFail($id);
        return $contract_specificPurpose;
    }

    public function store(array $data)
    {
        $this->validate($data);
        $contractSpecificPurpose = new ContractSpecificPurpose();
        $contractSpecificPurpose->suppliers_id = $data['suppliers_id'];
        $contractSpecificPurpose->cost_centers_id = $data['cost_centers_id'];
        $contractSpecificPurpose->chart_of_accounts_id = $data['chart_of_accounts_id'];
        $contractSpecificPurpose->start_period = $data['start_period'];
        $contractSpecificPurpose->end_period = $data['end_period'];
        $contractSpecificPurpose->payment_methods_id = $data['payment_methods_id'];
        $contractSpecificPurpose->object = $data['object'];
        $contractSpecificPurpose->price = $data['price'];
        $contractSpecificPurpose->day_of_maturities = $data['day_of_maturities'];
        $contractSpecificPurpose->readjustment_index = $data['readjustment_index'];
        $contractSpecificPurpose->early_termination = $data['early_termination'];
        $contractSpecificPurpose->early_termination_penalty = $data['early_termination_penalty'];
        $contractSpecificPurpose->bank = $data['bank'];
        $contractSpecificPurpose->agency = $data['agency'];
        $contractSpecificPurpose->account = $data['account'];
        $contractSpecificPurpose->observation = $data['observation'];
        $contractSpecificPurpose->save();
        $this->generateParcelings($contractSpecificPurpose->id, $contractSpecificPurpose->start_period, $contractSpecificPurpose->end_period, $contractSpecificPurpose->price, $contractSpecificPurpose->day_of_maturities, $contractSpecificPurpose->payment_methods_id);
        return $contractSpecificPurpose;
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
                'contract_specific_purposes_id' => $contract_recurrent_id,
                'planned_date' => $due_date,
                'monthly_value' => $monthly_value,
                'status_id' => self::STATUS['Previsto'],
                'payment_methods_id' => $payment_methods_id,
                'created_at' => $current_date,
            ];
            $due_date->modify('first day of +1 month');
            $result = ContractSpecificPurposeParcelings::insert($data);
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
        $contractsFiles->contract_specific_purposes_id = $data['contract_specificPurpose_id'];
        $contractsFiles->file_url = $path;
        $contractsFiles->save();
        return $contractsFiles;
    }

    public function list_files($id)
    {
        $contractsFiles = ContractsFiles::where([['contract_specific_purposes_id', $id]])->get();
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
                'contract_specific_purposes_id' => $data['contract_specific_purposes_id'],
                'planned_date' => $planned_date->format('Y-m-d'),
                'monthly_value' => $data['monthly_value'],
                'payment_methods_id' => $data['payment_methods_id'],
                'status_id' => self::STATUS['Previsto'],
                'created_at' => $current_date,
            ];

            $planned_date->modify('first day of +1 month');

            $result = ContractSpecificPurposeParcelings::insert($insert);
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
        $contract_specificPurpose = $this->show($data['id']);
        $contract_specificPurpose->suppliers_id = $data['suppliers_id'] ?? $contract_specificPurpose->suppliers_id;
        $contract_specificPurpose->cost_centers_id = $data['cost_centers_id'] ?? $contract_specificPurpose->cost_centers_id;
        $contract_specificPurpose->chart_of_accounts_id = $data['chart_of_accounts_id'] ?? $contract_specificPurpose->chart_of_accounts_id;
        $contract_specificPurpose->start_period = $data['start_period'] ?? $contract_specificPurpose->start_period;
        $contract_specificPurpose->end_period = $data['end_period'] ?? $contract_specificPurpose->end_period;
        $contract_specificPurpose->payment_methods_id = $data['payment_methods_id'] ?? $contract_specificPurpose->payment_methods_id;
        $contract_specificPurpose->object = $data['object'] ?? $contract_specificPurpose->object;
        $contract_specificPurpose->price = $data['price'] ?? $contract_specificPurpose->price;
        $contract_specificPurpose->day_of_maturities = $data['day_of_maturities'] ?? $contract_specificPurpose->day_of_maturities;
        $contract_specificPurpose->readjustment_index = $data['readjustment_index'] ?? $contract_specificPurpose->readjustment_index;
        $contract_specificPurpose->early_termination = $data['early_termination'] ?? $contract_specificPurpose->early_termination;
        $contract_specificPurpose->early_termination_penalty = $data['early_termination_penalty'] ?? $contract_specificPurpose->early_termination_penalty;
        $contract_specificPurpose->bank = $data['bank'] ?? $contract_specificPurpose->bank;
        $contract_specificPurpose->agency = $data['agency'] ?? $contract_specificPurpose->agency;
        $contract_specificPurpose->account = $data['account'] ?? $contract_specificPurpose->account;
        $contract_specificPurpose->observation = $data['observation'] ?? $contract_specificPurpose->observation;
        $contract_specificPurpose->save();
        return $contract_specificPurpose;
    }

    public function installments_update(array $data)
    {
        $this->installments_validate($data);
        $contractSpecificPurposeParcelings = ContractSpecificPurposeParcelings::find($data['id']);
        $contractSpecificPurposeParcelings->planned_date = $data['planned_date'] ?? $contractSpecificPurposeParcelings->planned_date;
        $contractSpecificPurposeParcelings->monthly_value = $data['monthly_value'] ?? $contractSpecificPurposeParcelings->monthly_value;
        $contractSpecificPurposeParcelings->payment_methods_id = $data['payment_methods_id'] ?? $contractSpecificPurposeParcelings->payment_methods_id;
        $contractSpecificPurposeParcelings->save();
        return $contractSpecificPurposeParcelings;
    }

    public function installments_commitment(array $data)
    {
        $path = $data['file']->store('files');
        $contractSpecificPurposeParcelings = ContractSpecificPurposeParcelings::find($data['id']);
        $contractSpecificPurposeParcelings->fiscal_note = $data['fiscal_note'] ?? $contractSpecificPurposeParcelings->fiscal_note;
        $contractSpecificPurposeParcelings->emission_date = $data['emission_date'] ?? $contractSpecificPurposeParcelings->emission_date;
        $contractSpecificPurposeParcelings->payment_methods_id = $data['payment_methods_id'] ?? $contractSpecificPurposeParcelings->payment_methods_id;
        $contractSpecificPurposeParcelings->due_date = $data['due_date'] ?? $contractSpecificPurposeParcelings->due_date;
        $contractSpecificPurposeParcelings->commitment_value = $data['commitment_value'] ?? $contractSpecificPurposeParcelings->commitment_value;
        $contractSpecificPurposeParcelings->file_url = $path ?? $contractSpecificPurposeParcelings->file_url;
        $contractSpecificPurposeParcelings->status_id = self::STATUS['Compromissado'] ?? $contractSpecificPurposeParcelings->status_id;
        $contractSpecificPurposeParcelings->save();
        return $contractSpecificPurposeParcelings;
    }

    public function installments_destroy($id)
    {
        $contractSpecificPurposeParcelings = $this->getInstallments($id);
        $contractSpecificPurposeParcelings->delete();
        return $contractSpecificPurposeParcelings;
    }
    public function destroy($id)
    {
        $contract_specificPurpose = $this->show($id);
        $contract_specificPurpose->delete();
        return $contract_specificPurpose;
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
                'contract_specific_purposes_id' => 'required',
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
