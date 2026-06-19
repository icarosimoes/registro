<?php

namespace App\Services\Register;

use App\Exceptions\ValidationException;
use App\Models\Register\InputGroup;
use App\Models\Register\Inputs;
use App\Models\Register\InputsSuppliers;
use App\Models\Register\Supplier;
use App\Models\Register\Unit;
use App\Services\Service;
use Illuminate\Support\Facades\Validator;

class InputsService extends Service
{
    public function index($id)
    {
        $inputs = Inputs::where([['input_groups_id', $id]])->get();
        return $inputs;
    }

    public function getInputsSuppliers($id)
    {
        return InputsSuppliers::where([['inputs_id', $id]])->get();
    }

    public function getSupplier()
    {
        $supplier = Supplier::all();
        return $supplier;
    }

    public function getUnit()
    {
        $units = Unit::all();
        return $units;
    }

    public function getInputGroup($id)
    {
        $getInputGroup = InputGroup::where([['id', $id]])->first();
        return $getInputGroup;
    }

    public function show($id)
    {
        $inputs = Inputs::findOrFail($id);
        return $inputs;
    }
    public function store(array $data)
    {
        $this->validate($data);
        $inputs = new Inputs();
        $inputs->input_groups_id = $data['input_group'];
        $codeFormart = str_replace("-", "", $data['code']);
        $inputs->code = $codeFormart;
        $inputs->description = $data['description'];
        $inputs->units_id = preg_replace("/[^0-9]/", "", $data['unit']);
        $inputs->unit_cost = str_replace(",", ".", str_replace('.', '', $data['unit_cost']));
        $inputs->comments = $data['comments'];
        $inputs->save();

        $insertID = $inputs->id;
        //inputsSuppliers - insert
        if ($data['suppliers']) {
            $suppliers = explode(',', $data['suppliers']);
            for ($i = 0; $i < count($suppliers); $i++) {
                $data = [
                    'inputs_id' => $insertID,
                    'suppliers_id' => $suppliers[$i],
                ];
                InputsSuppliers::insert($data);
            }
        }
        return $inputs;
    }

    public function checkCode(array $data)
    {
        $codeFormat = str_replace("-", "", $data['code']);
        $code = Inputs::where([['code', $codeFormat]])->first();
        if ($code == null) {
            return false;
        } else {
            return true;
        }
    }

    public function update(array $data): Inputs
    {
        $this->validate($data);
        $inputs = $this->show($data['id']);
        $codeFormart = str_replace("-", "", $data['code']);
        $inputs->input_groups_id = $data['input_group'] ?? $inputs->input_group;
        $inputs->code = $codeFormart ?? $inputs->code;
        $inputs->description = $data['description'] ?? $inputs->description;
        $inputs->units_id = preg_replace("/[^0-9]/", "", $data['unit']) ?? $inputs->unit;
        $inputs->unit_cost = str_replace(",", ".", str_replace('.', '', $data['unit_cost'])) ?? $inputs->unit_cost;
        $inputs->comments = $data['comments'] ?? $inputs->comments;
        $inputs->save();
        //inputsSupplier - insert
        if ($data['suppliers']) {
            $suppliers = explode(',', $data['suppliers']);
            $this->deleteInputsSuppliers($inputs->id);
            for ($i = 0; $i < count($suppliers); $i++) {
                $data = [
                    'inputs_id' => $inputs->id,
                    'suppliers_id' => $suppliers[$i],
                ];
                InputsSuppliers::insert($data);
            }
        }
        return $inputs;
    }

    public function deleteInputsSuppliers($inputs_id)
    {
        $inputsSuppliers = InputsSuppliers::where([['inputs_id', $inputs_id]])->delete();
        return $inputsSuppliers;
    }

    public function destroy($id)
    {
        try {
            $this->deleteInputsSuppliers($id); // delete cascade inputsSuppliers
            $inputs = $this->show($id);
            $inputs->delete();
            return $inputs;
        } catch (\Exception $e) {
            return redirect()->back()->with('alert', 'Desculpe, não é possível excluir o dado selecionado, Verifique se o dado não está sendo ultilizado em outras tabelas!');
        }
    }

    private function validate(array $data): bool
    {
        $validator = Validator::make(
            $data,
            [
                'code' => 'required',
                'description' => 'required',
                'input_group' => 'required',
                'unit' => 'required',
                'unit_cost' => 'required',
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
