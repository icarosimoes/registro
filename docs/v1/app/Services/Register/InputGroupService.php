<?php

namespace App\Services\Register;

use App\Exceptions\ValidationException;
use App\Models\Register\InputGroup;
use App\Services\Service;
use Illuminate\Support\Facades\Validator;

class InputGroupService extends Service
{
    public function index()
    {
        $inputGroup = InputGroup::all();
        return $inputGroup;
    }

    public function show($id)
    {
        $inputGroup = InputGroup::findOrFail($id);
        return $inputGroup;
    }
    public function store(array $data)
    {
        $this->validate($data);
        $inputGroup = new InputGroup();
        $codeFormart = str_replace("-", "", $data['code']);
        $inputGroup->code = $codeFormart;
        $inputGroup->description = $data['description'];
        $inputGroup->save();
        return $inputGroup;
    }

    public function checkCode(array $data)
    {
        $codeFormat = str_replace("-", "", $data['code']);
        $code = InputGroup::where([['code', $codeFormat]])->first();
        if ($code == null) {
            return false;
        } else {
            return true;
        }
    }

    public function update(array $data): InputGroup
    {
        $this->validate($data);
        $inputGroup = $this->show($data['id']);
        $codeFormart = str_replace("-", "", $data['code']);
        $inputGroup->code = $codeFormart ?? $inputGroup->code;
        $inputGroup->description = $data['description'] ?? $inputGroup->description;
        $inputGroup->save();
        return $inputGroup;
    }

    public function destroy($id)
    {
        try {
            $inputGroup = $this->show($id);
            $inputGroup->delete();
            return $inputGroup;
        } catch (\Exception $e) {
            return redirect()->back()->with('alert', 'Desculpe, não é possível excluir o dado selecionado, Verifique se o dado não está sendo ultilizado em outras funcionalidades!');
        }

    }


    private function validate(array $data): bool
    {
        $validator = Validator::make(
            $data,
            [
                'code' => 'required',
                'description' => 'required',
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
