<?php

namespace App\Services\Sale;

use App\Exceptions\ValidationException;
use App\Models\CostCenter;
use App\Models\Register\ChartOfAccounts;
use App\Models\Register\Client;
use App\Models\Register\PaymentMethods;
use App\Models\Register\Product;
use App\Models\Sales\SaleProposal;
use App\Models\Sales\SaleProposalProducts;
use App\Models\Sales\StatusSaleProposal;
use App\Services\Service;
use DateTime;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SaleProposalService extends Service
{

    public function index()
    {
        $saleProposal = SaleProposal::all();
        return $saleProposal;
    }

    public function show(int $id): SaleProposal
    {
        return SaleProposal::findOrFail($id);
    }

    //####### Payment Methods #######
    public function getPaymentMethods()
    {
        $paymentMethods = PaymentMethods::all();
        return $paymentMethods;
    }
    //####### end Payment Methods #######

    //####### Client #######
    public function getClient()
    {
        $client = Client::all();
        return $client;
    }
    public function getClientID($id)
    {
        $client = Client::find($id);
        return $client;
    }
    //####### end Client #######

    //####### Cosnt Center #######
    public function getCostCenter()
    {
        $costCenter = CostCenter::all();
        return $costCenter;
    }
    public function getCostCenterID($id)
    {
        $costCenter = CostCenter::find($id);
        return $costCenter;
    }
    //####### end Cosnt Center #######

    //####### Product #######
    public function getProduct()
    {
        $product = Product::all();
        return $product;
    }
    public function getProductID($id)
    {
        $product = Product::find($id);
        return $product;
    }
    //####### end Product #######

    //####### Status Sale Proposals #######
    public function StatusSaleProposals()
    {
        $statusSaleProposals = StatusSaleProposal::all();
        return $statusSaleProposals;
    }
    public function StatusSaleProposalsID($id)
    {
        $statusSaleProposals = StatusSaleProposal::find($id);
        return $statusSaleProposals;
    }
    //####### end Status Sale Proposals #######

    //####### Chart of accounts #######
    public function getChartOfAccounts()
    {
        $chartOfAccounts = ChartOfAccounts::all();
        return $chartOfAccounts;
    }
    public function getChartOfAccountsID($id)
    {
        $chartOfAccounts = ChartOfAccounts::find($id);
        return $chartOfAccounts;
    }
    //####### end Chart of accounts #######

    //####### Collection product and Chart Of Account ######
    public function getCollection(array $data)
    {
        $product = Product::find($data['product_search_selected']);
        $chartOfAccounts = ChartOfAccounts::find($data['chartOfAccounts_search_selected']);
        $data = [
            'product' => $product,
            'product_inut' => $product['units']->description,
            'chartOfAccounts' => $chartOfAccounts,
        ];
        return $data;
    }
    //####### end Collection product and Chart Of Account ######

    public function getProductCollectionID($id)
    {
        $saleProposalProduct = SaleProposalProducts::where([['sale_proposals_id', $id]])->get();
        foreach ($saleProposalProduct as $product) {
            $data[] = [
                'products' => $product['products'],
                'chart_of_accounts' => $product['chart_of_accounts'],
            ];
        }
        return $saleProposalProduct;
    }

    public function store(array $data)
    {
        $this->validate($data);
        DB::beginTransaction();
        try {
            $saleProposal = new SaleProposal();
            $saleProposal->clients_id = $data['clients_id'];
            $saleProposal->cost_centers_id = $data['cost_centers_id'];
            $expiration_time = new DateTime($data['expiration_time']);
            $saleProposal->expiration_time = $expiration_time->format('Y-m-d');
            $deadline = new DateTime($data['deadline']);
            $saleProposal->deadline = $deadline->format('Y-m-d');
            $saleProposal->payment_methods_id = $data['payment_methods_id'];
            $saleProposal->installments = $data['installments'];
            $saleProposal->discount = $this->ConvertValor($data['discount']);
            $saleProposal->total = $this->ConvertValor($data['total']);
            $saleProposal->status_sale_proposals_id = 1;
            $saleProposal->save();
            $insertID = $saleProposal->id;
            //inserir produtos
            $product_id = explode(',', $data['product_id'][0]);
            $product_chartOfAccount_id = explode(',', $data['product_chartOfAccount_id'][0]);
            $product_unit = explode(',', $data['product_unit'][0]);
            $unit_cost_product = explode(',', $data['unit_cost_product'][0]);
            $product_amount = explode(',', $data['product_amount'][0]);
            $product_total = explode(',', $data['product_total'][0]);

            for ($i = 0; $i < count($product_id); $i++) {
                $dataProducts = [
                    'sale_proposals_id' => $insertID,
                    'products_id' => $product_id[$i],
                    'chart_of_accounts_id' => $product_chartOfAccount_id[$i],
                    'product_unit' => $product_unit[$i],
                    'amount' => $product_amount[$i],
                    'total' => $product_total[$i],
                    'product_unit_cost' => $unit_cost_product[$i],
                ];
                $insertProducts = SaleProposalProducts::insert($dataProducts);
            }
            DB::commit();
            return $saleProposal;

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->withErrors([
                'message' => $e->getMessage(),
            ]);
        }

    }

    public function ConvertValor($valor)
    {
        $verificaPonto = ".";
        if (strpos("[" . $valor . "]", "$verificaPonto")):
            $valor = str_replace('.', '', $valor);
            $valor = str_replace(',', '.', $valor);
        else:
            $valor = str_replace(',', '.', $valor);
        endif;

        return $valor;
    }

    public function update(array $data)
    {
        $this->validate($data);
        DB::beginTransaction();
        try {
            $saleProposal = $this->show($data['id']);
            $saleProposal->clients_id = $data['clients_id'];
            $saleProposal->cost_centers_id = $data['cost_centers_id'];
            $expiration_time = new DateTime($data['expiration_time']);
            $saleProposal->expiration_time = $expiration_time->format('Y-m-d');
            $deadline = new DateTime($data['deadline']);
            $saleProposal->deadline = $deadline->format('Y-m-d');
            $saleProposal->payment_methods_id = $data['payment_methods_id'];
            $saleProposal->installments = $data['installments'];
            $saleProposal->discount = $this->ConvertValor($data['discount']);
            $saleProposal->total = $this->ConvertValor($data['total']);
            $saleProposal->status_sale_proposals_id = $data['status_sale_proposals_id'];
            $saleProposal->save();
            $insertID = $saleProposal->id;

            if (isset($data['product_id'][0])) {

                //preparando tabela para atualização, removendo os dados antigos e atualizando com os dados novos.
                 SaleProposalProducts::where([['sale_proposals_id', $insertID]])->forceDelete();
                //inserir produtos
                $product_id = explode(',', $data['product_id'][0]);
                $product_chartOfAccount_id = explode(',', $data['product_chartOfAccount_id'][0]);
                $product_unit = explode(',', $data['product_unit'][0]);
                $unit_cost_product = explode(',', $data['unit_cost_product'][0]);
                $product_amount = explode(',', $data['product_amount'][0]);
                $product_total = explode(',', $data['product_total'][0]);

                for ($i = 0; $i < count($product_id); $i++) {
                    $dataProducts = [
                        'sale_proposals_id' => $insertID,
                        'products_id' => $product_id[$i],
                        'chart_of_accounts_id' => $product_chartOfAccount_id[$i],
                        'product_unit' => $product_unit[$i],
                        'amount' => $product_amount[$i],
                        'total' => $product_total[$i],
                        'product_unit_cost' => $unit_cost_product[$i],
                    ];
                    $insertProducts = SaleProposalProducts::insert($dataProducts);
                }
                DB::commit();
            }
            return $saleProposal;

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->withErrors([
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function destroy($id)
    {
        //remover dependências de saleProposalProduct, remover em cascata
        SaleProposalProducts::where([['sale_proposals_id', $id]])->delete();
        $saleProposal = $this->show($id);
        $saleProposal->delete();
        return $saleProposal;
    }

    private function validate(array $data): bool
    {
        $validator = Validator::make(
            $data,
            [
                'clients_id' => 'required',
                'cost_centers_id' => 'required',
                'expiration_time' => 'required',
                'deadline' => 'required',
                'payment_methods_id' => 'required',
                'installments' => 'required',
                'discount' => 'required',
                'total' => 'required',

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
