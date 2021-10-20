<?php

namespace App\Services\Register;

use App\Exceptions\ValidationException;
use App\Models\Register\GroupProduct;
use App\Models\Register\Inputs;
use App\Models\Register\Product;
use App\Models\Register\ProductInputs;
use App\Models\Register\ProductProduct;
use App\Models\Register\Unit;
use App\Services\Service;
use Illuminate\Support\Facades\Validator;
use NumberFormatter;

class ProductService extends Service
{

    public function index($id)
    {
        $product = Product::where([['group_products_id', $id]])->get();
        return $product;
    }

    public function getProductInput($id)
    {
        $product = ProductInputs::where([['products_id', $id]])->get();
        return $product;
    }

    public function getProductProducts($id)
    {
        $product = ProductProduct::where([['products_id', $id]])->get();
        return $product;
    }

    public function getInputs()
    {
        $inputs = Inputs::all();
        return $inputs;
    }
    public function getProduct()
    {
        $product = Product::all();
        return $product;
    }

    public function getInputID(array $data)
    {
        $inputs = Inputs::findOrFail($data['input_search_selected']);
        $data = [
            'id' => $inputs->id,
            'code' => $inputs->code,
            'description' => $inputs->description,
            'unit' => $inputs['units']->description,
            'unit_cost' => $inputs->unit_cost,
        ];
        return $data;
    }
    public function getProductID(array $data)
    {
        $product = Product::findOrFail($data['product_search_selected']);
        $data = [
            'id' => $product->id,
            'code' => $product->code,
            'description' => $product->description,
            'unit' => $product['units']->description,
            'unit_cost' => $product->unit_cost,
        ];
        return $data;
    }

    public function getProductProduct($id)
    {
        $product = ProductProduct::where([['products_id', $id]])->get();
        foreach ($product as $products) {
            $data[] = $this->show($products->products);
        }
        return $data;
    }

    public function getUnits()
    {
        $units = Unit::all();
        return $units;
    }

    public function getGroupProduct($id)
    {
        $groupProduct = GroupProduct::findOrFail($id);
        return $groupProduct;
    }

    public function show(int $id): Product
    {
        return Product::findOrFail($id);
    }

    public function store(array $data)
    {
        $this->validate($data);
        $product = new Product();
        $product->group_products_id = $data['group_products_id'];
        $product->code = $data['code'];
        $product->units_id = $data['units_id'];
        $product->description = $data['description'];
        $product->unit_cost = $this->ConvertValor($data['costs_unit_cost']);
        $product->sale_price = $this->ConvertValor($data['costs_sale_price']);
        $product->contribution_margin = $this->ConvertValor($data['costs_contribution_margin_real']);
        $product->costs_contribution_margin_percent = $this->ConvertValor($data['costs_contribution_margin_percent']);
        $product->save();
        $InsertID = $product->id;

        if (isset($data['id_input'][0])) {
            //product inpust
            $id_input = explode(',', $data['id_input'][0]);
            $code_input = explode(',', $data['code_input'][0]);
            $description_input = explode(',', $data['description_input'][0]);
            $unit_input = explode(',', $data['unit_input'][0]);
            $amount_input = explode(',', $data['amount_input'][0]);
            $unit_cost_input = explode(',', $data['unit_cost_input'][0]);
            $total_input = explode(',', $data['total_input'][0]);
            
            for ($i = 0; $i < count($id_input); $i++) {
                $dataInputs = [
                    'products_id' => $InsertID,
                    'inputs_id' => $id_input[$i],
                    'code_input' => $code_input[$i],
                    'description_input' => $description_input[$i],
                    'unit_input' => $unit_input[$i],
                    'amount_input' => $amount_input[$i],
                    'unit_cost' => $unit_cost_input[$i],
                    'total_input' => $total_input[$i],
                ];
                ProductInputs::insert($dataInputs);
            }
        }
        if (isset($data['id_product'][0])) {
            //product products
            $id_product = explode(',', $data['id_product'][0]);
            $code_product = explode(',', $data['code_product'][0]);
            $description_product = explode(',', $data['description_product'][0]);
            $unit_product = explode(',', $data['unit_product'][0]);
            $amount_product = explode(',', $data['amount_product'][0]);
            $unit_cost_product = explode(',', $data['unit_cost_product'][0]);
            $total_product = explode(',', $data['total_product'][0]);
            for ($i = 0; $i < count($id_product); $i++) {
                $dataProduct = [
                    'products_id' => $InsertID,
                    'products' => $id_product[$i],
                    'code_product' => $code_product[$i],
                    'description_product' => $description_product[$i],
                    'unit_product' => $unit_product[$i],
                    'amount_product' => $amount_product[$i],
                    'unit_cost_product' => $unit_cost_product[$i],
                    'total_product' => $total_product[$i],
                ];
                ProductProduct::insert($dataProduct);
            }
        }
         return $product;
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

    public function update(array $data)
    {
        $this->validate($data);
        $product = $this->show($data['id']);
        $product->group_products_id = $data['group_products_id'];
        $product->code = $data['code'];
        $product->units_id = $data['units_id'];
        $product->description = $data['description'];
        $product->unit_cost = $this->ConvertValor($data['costs_unit_cost']);
        $product->sale_price = $this->ConvertValor($data['costs_sale_price']);
        $product->contribution_margin = $this->ConvertValor($data['costs_contribution_margin_real']);
        $product->costs_contribution_margin_percent = $this->ConvertValor($data['costs_contribution_margin_percent']);
        $product->save();
        $InsertID = $product->id;

       
        if (isset($data['id_input'][0])) {
            
            //preparando tabela para atualização, removendo os dados antigos e atualizando com os dados novos.
            ProductInputs::where([['products_id', $InsertID]])->forceDelete();
            
            //product inpust
            $id_input = explode(',', $data['id_input'][0]);
            $code_input = explode(',', $data['code_input'][0]);
            $description_input = explode(',', $data['description_input'][0]);
            $unit_input = explode(',', $data['unit_input'][0]);
            $amount_input = explode(',', $data['amount_input'][0]);
            $unit_cost = explode(',', $data['unit_cost'][0]);
            $total_input = explode(',', $data['total_input'][0]);
            for ($i = 0; $i < count($id_input); $i++) {
                $dataInputs = [
                    'products_id' => $InsertID,
                    'inputs_id' => $id_input[$i],
                    'code_input' => $code_input[$i],
                    'description_input' => $description_input[$i],
                    'unit_input' => $unit_input[$i],
                    'amount_input' => $amount_input[$i],
                    'unit_cost' => $unit_cost[$i],
                    'total_input' => $total_input[$i],
                ];
               
               ProductInputs::insert($dataInputs);
            }
        }
        
        if (isset($data['id_product'][0])) {
            //preparando tabela para atualização, removendo os dados antigos e atualizando com os dados novos.
            $v = ProductProduct::where([['products_id', $InsertID]])->forceDelete();
            //product products
            $id_product = explode(',', $data['id_product'][0]);
            $code_product = explode(',', $data['code_product'][0]);
            $description_product = explode(',', $data['description_product'][0]);
            $unit_product = explode(',', $data['unit_product'][0]);
            $amount_product = explode(',', $data['amount_product'][0]);
            $unit_cost_product = explode(',', $data['unit_cost_product'][0]);
            $total_product = explode(',', $data['total_product'][0]);
            for ($i = 0; $i < count($id_product); $i++) {
                $dataProduct = [
                    'products_id' => $InsertID,
                    'products' => $id_product[$i],
                    'code_product' => $code_product[$i],
                    'description_product' => $description_product[$i],
                    'unit_product' => $unit_product[$i],
                    'amount_product' => $amount_product[$i],
                    'unit_cost_product' => $unit_cost_product[$i],
                    'total_product' => $total_product[$i],
                ];
                ProductProduct::insert($dataProduct);
            }
        }
        return $product;

    }

    public function destroy($id)
    {
        //remover Dependências da tabela
        ProductInputs::where([['products_id', $id]])->delete();
        ProductProduct::where([['products_id', $id]])->delete();
        //remover produto
        $product = $this->show($id);
        $product->delete();
        return $product;
    }

    private function validate(array $data): bool
    {
        $validator = Validator::make(
            $data,
            [
                'group_products_id' => 'required',
                'code' => 'required',
                'units_id' => 'required',
                'description' => 'required',
                'costs_sale_price' => 'required',
                'costs_unit_cost' => 'required',
                'costs_contribution_margin_real' => 'required',
                'costs_contribution_margin_percent' => 'required'
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
