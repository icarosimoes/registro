<?php

namespace App\Services;

class Service 
{
    public function getDefaultMessages()
    {
        return [
            'same'      => 'O :attribute e :other devem ser o mesmo.',
            'size'      => 'O :attribute deve ter exatamente :size.',
            'between'   => 'O valor do campo :attribute não está entre :min - :max.',
            'in'        => 'O campo :attribute deve ter um dos seguintes valores: :values.',
            'required'  => 'O campo :attribute é obrigatório.',
            'email'     => 'O campo :attribute deve ter um e-mail válido.',
            'date'      => 'O campo :attribute deve ter uma data válida.',
            'unique'    => ':input já existe.'
        ];
    }
}