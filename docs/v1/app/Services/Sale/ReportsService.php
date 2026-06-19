<?php

namespace App\Services\Sale;

use App\Models\Register\Product;
use App\Models\Register\ProductProduct;
use App\Models\Sales\Billing;
use App\Services\Service;

class ReportsService extends Service
{
    public function reportClients(array $filter = null)
    {
        if (isset($filter['filter_date_params1']) && isset($filter['filter_date_params2'])) {
            $clients = Billing::leftJoin('sale_proposals', 'sale_proposals.id', '=', 'billings.sale_proposals_id')
                ->leftJoin('clients', 'clients.id', '=', 'sale_proposals.clients_id')
                ->select(['clients.id', 'clients.nome', 'billings.total', 'billings.acumulative_billing'])
                ->whereBetween('emission_date', [$filter['filter_date_params1'], $filter['filter_date_params2']])
                ->get();
            return $clients;
        } else {
            $clients = Billing::leftJoin('sale_proposals', 'sale_proposals.id', '=', 'billings.sale_proposals_id')
                ->leftJoin('clients', 'clients.id', '=', 'sale_proposals.clients_id')
                ->select(['clients.id', 'clients.nome', 'billings.total', 'billings.acumulative_billing'])
                ->get();
            return $clients;
        }
    }

    public function reportProducts(array $filter = null)
    {
        if (isset($filter['filter_date_params1']) && isset($filter['filter_date_params2'])) {
            $products = Billing::leftJoin('sale_proposals', 'sale_proposals.id', '=', 'billings.sale_proposals_id')
                ->leftJoin('sale_proposal_products', 'sale_proposal_products.sale_proposals_id', '=', 'sale_proposals.id')
                ->leftJoin('products', 'products.id', '=', 'sale_proposal_products.products_id')
                ->select(['products.*'])
                ->whereBetween('created_at', [$filter['filter_date_params1'], $filter['filter_date_params2']])
                ->get();
        } else {
            $products = Billing::leftJoin('sale_proposals', 'sale_proposals.id', '=', 'billings.sale_proposals_id')
                ->leftJoin('sale_proposal_products', 'sale_proposal_products.sale_proposals_id', '=', 'sale_proposals.id')
                ->leftJoin('products', 'products.id', '=', 'sale_proposal_products.products_id')
                ->select(['products.*'])
                ->get();
        }

        $collectionProductProducts = $this->getProductProduct($products);

        $productConvertCollections = collect($products->toArray());

        if ($collectionProductProducts) {
            foreach ($collectionProductProducts as $collectionProductProduct) {
                $mergeCollection = $productConvertCollections->push($collectionProductProduct->toArray());
            }
            return $mergeCollection;
        }

        return $products;
    }

    public function reportInputs(array $filter = null)
    {
        if (isset($filter['filter_date_params1']) && isset($filter['filter_date_params2'])) {
            $inputs = Billing::leftJoin('sale_proposals', 'sale_proposals.id', '=', 'billings.sale_proposals_id')
                ->leftJoin('sale_proposal_products', 'sale_proposal_products.sale_proposals_id', '=', 'sale_proposals.id')
                ->leftJoin('products', 'products.id', '=', 'sale_proposal_products.products_id')
                ->leftJoin('product_inputs', 'product_inputs.products_id', '=', 'products.id')
                ->select(['product_inputs.*'])
                ->whereBetween('created_at', [$filter['filter_date_params1'], $filter['filter_date_params2']])
                ->get();
        } else {
            $inputs = Billing::leftJoin('sale_proposals', 'sale_proposals.id', '=', 'billings.sale_proposals_id')
                ->leftJoin('sale_proposal_products', 'sale_proposal_products.sale_proposals_id', '=', 'sale_proposals.id')
                ->leftJoin('products', 'products.id', '=', 'sale_proposal_products.products_id')
                ->leftJoin('product_inputs', 'product_inputs.products_id', '=', 'products.id')
                ->select(['product_inputs.*'])
                ->get();
        }
        return $inputs;
    }

    public function getProductProduct($products)
    {
        $collection = null;
        foreach ($products as $product) {
            $productProduct = ProductProduct::where([['products_id', $product->id]])->first();
            $productResult = Product::where([['id', $productProduct->products]])->first();
            if ($productProduct) {
                $dataCollectionProductProduct[] = collect($productResult);
            }
        }
        if (!empty($dataCollectionProductProduct)) {
            $collection = $dataCollectionProductProduct;
        }
        return $collection;
    }
}
