<?php

namespace App\Services\Sale;

use App\Exceptions\ValidationException;
use App\Models\Register\PaymentMethods;
use App\Models\Sales\Billing;
use App\Models\Sales\BillingParceling;
use App\Models\Sales\SaleProposal;
use App\Models\Sales\SaleProposalProducts;
use App\Models\User;
use App\Services\Service;
use DateTime;
use Illuminate\Support\Facades\Validator;

class BillingService extends Service
{

    public function index()
    {
        $billing = Billing::all();
        $data = array();
        foreach ($billing as $item) {
            $billingParceling = BillingParceling::where([['billings_id', $item->id]])->get();
            $data[] = array(
                'id' => $item->id,
                'client' => $item['sale_proposals']['clients']->nome,
                'emission_date' => $item->emission_date,
                'sale_proposals_id' => $item->sale_proposals_id,
                'total' => $item->total,
                'parcels' => $billingParceling->count(),
            );
        }
        return $data;
    }

    //parcels---------------------------------------------------------------------------------
    public function getBillingParcels($id)
    {
        $billingParceling = BillingParceling::where([['billings_id', $id]])->get();
        return $billingParceling;
    }

    public function store_installments(array $data)
    {
        //antes de inserir o registro é preciso verificar se a soma das parcelas não ultrapassa o valor da proposta.
        $billing = $this->show($data['billings_id']);

        $cauculateValueTotalParcels = $this->cauculateValueTotalParcels($data['billings_id']);
        $total = $cauculateValueTotalParcels + $data['total_price'];

        if ($total > $billing->acumulative_billing) {
            return ['success' => false, 'error' => 'A soma das PARCELAS excede o limite da proposta.'];
        } else {
            $installment = new BillingParceling();
            $installment->billings_id = $data['billings_id'];
            $installment->expiration_date = $data['expiration_date'];
            $installment->price = $data['price'];
            $installment->payment_methods_id = $data['payment_methods_search_selected'];
            $installment->deadline = $data['deadline'];
            $installment->tax = $data['tax'];
            $installment->date_deadline = $data['date_deadline'];
            $installment->total_price = $data['total_price'];
            $date_now = new DateTime();
            $installment->created_at = $date_now->format('Y-m-d H:i:s');
            $installment->save();
            if ($installment) {
                return ['success' => true];
            } else {
                return ['success' => false];
            }
        }

    }

    public function cauculateValueTotalParcels($id)
    {
        $calculate_total = 0;
        $billing_parcels = $this->getBillingParcels($id);
        foreach ($billing_parcels as $item) {
            $calculate_total += $item->price;
        }
        return $calculate_total;
    }

    public function updateTotal($id, $total)
    {
        $billing = $this->show($id);
        $billing->total = $total;
        $billing->save();
        return $billing;
    }
    public function show_parcels(int $id)
    {
        return BillingParceling::findOrFail($id);
    }
    public function update_installments(array $data)
    {

        //antes de editar o registro é preciso verificar se a soma das parcelas não ultrapassa o valor da proposta.
        $billing = $this->show($data['billings_id']);

        $cauculateValueTotalParcels = $this->cauculateValueTotalParcels($data['billings_id']);
        $total = $cauculateValueTotalParcels + $data['total_price'];

        if ($total > $billing->acumulative_billing) {
            return ['success' => false, 'error' => 'A soma das PARCELAS excede o limite da proposta.'];
        } else {
            $installments = $this->show_parcels($data['id']);
            $installments->expiration_date = $data['expiration_date'] ?? $installments->expiration_date;
            $installments->price = $data['price'] ?? $installments->price;
            $installments->payment_methods_id = $data['payment_methods_search_selected'] ?? $installments->payment_methods_id;
            $installments->deadline = $data['deadline'] ?? $installments->deadline;
            $installments->tax = $data['tax'] ?? $installments->tax;
            $installments->date_deadline = $data['date_deadline'] ?? $installments->date_deadline;
            $installments->total_price = $data['total_price'] ?? $installments->total_price;
            $installments->save();
            return $installments;
        }
    }
    public function destroy_installments($id)
    {
        $installments = $this->show_parcels($id);
        $installments->delete();
        return $installments;
    }
    //end | parcels---------------------------------------------------------------------------------

    public function show(int $id): Billing
    {
        return Billing::findOrFail($id);
    }

    public function getPaymentMethods()
    {
        $paymentMethods = PaymentMethods::all();
        return $paymentMethods;
    }
    public function getSaleProposal()
    {
        $saleProposal = SaleProposal::where([['status_sale_proposals_id',2]])->get();
        return $saleProposal;
    }
    public function getSaleProposalID(array $data)
    {
        $saleProposal = SaleProposal::findOrFail($data['sale_proposal_id']);
        $saleProposalProducts = SaleProposalProducts::where([['sale_proposals_id', $data['sale_proposal_id']]])->get();
        foreach ($saleProposalProducts as $products) {
            $products['products'];
            $products['chart_of_accounts'];
        }
        return ['saleProposal' => $saleProposal, 'saleProposalProducts' => $saleProposalProducts];
    }
    public function getPaymentMethodsID(array $data)
    {
        $paymentMethods = PaymentMethods::find($data['payment_methods_search_selected']);
        return $paymentMethods;
    }
    public function store(array $data)
    {
        // try {
        $this->validate($data);
        //DB::beginTransaction();
        $path = $data['file']->store('files');
        $billing = new Billing();
        $billing->sale_proposals_id = $data['sale_proposal_id'];
        $billing->fiscal_note = $data['fiscal_note'];
        $billing->emission_date = $data['emission_date'];
        $billing->file = $path;
        $billing->acumulative_billing = $data['acumulative_billing'];
        $billing->balance = $data['balance'];
        $billing->total = $data['total'];
        $billing->save();
        $insertID = $billing->id;

        //parcels
        $parcels_expiration_date = explode(',', $data['parcels_expiration_date'][0]);
        $parcels_price = explode(',', $data['parcels_price'][0]);
        $parcels_payment_methods = explode(',', $data['parcels_payment_methods_id'][0]);
        $parcels_deadline = explode(',', $data['parcels_deadline'][0]);
        $parcels_tax = explode(',', $data['parcels_tax'][0]);
        $parcels_date_deadline = explode(',', $data['parcels_date_deadline'][0]);
        $parcels_price_total = explode(',', $data['parcels_price_total'][0]);

        for ($i = 0; $i < count($parcels_expiration_date); $i++) {
            $data = [
                'billings_id' => $insertID,
                'expiration_date' => $parcels_expiration_date[$i],
                'price' => $parcels_price[$i],
                'payment_methods_id' => $parcels_payment_methods[$i],
                'deadline' => $parcels_deadline[$i],
                'tax' => $parcels_tax[$i],
                'date_deadline' => $parcels_date_deadline[$i],
                'total_price' => $parcels_price_total[$i],
            ];
            BillingParceling::insert($data);
        }
        //DB::commit();
        // } catch (\Exception $e) {
        //   // DB::rollBack();
        //    return redirect()->back()->withInput()->withErrors([
        //     'message' => $e->getMessage(),
        //     'color' => "danger"
        //    ]);
        // }
        return $billing;
    }

    public function update(array $data)
    {
        //
    }

    public function destroy($id)
    {
        //remover parcelas, cascade
        BillingParceling::where([['billings_id', $id]])->delete();
        $billing = $this->show($id);
        $billing->delete();
        return $billing;
    }

    public function restore($id)
    {
        $user = User::withTrashed()->find($id);
        $user->restore();
        return $user;
    }

    private function validate(array $data): bool
    {
        $validator = Validator::make(
            $data,
            [
                'sale_proposal_id' => 'required',
                'fiscal_note' => 'required',
                'emission_date' => 'required',
                'file' => 'required|mimes:pdf|max:2048',
                'acumulative_billing' => 'required',
                'balance' => 'required',
                'total' => 'required',
                'parcels_expiration_date' => 'required',
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
