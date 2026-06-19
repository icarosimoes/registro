<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable = [
        'name',
        'email',
        'address',
        'cep',
        'telephone1',
        'telephone2',
        'cnpj',
        'state_registry',
        'municipal_registry',
        'city_id'
    ];
}
