<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ConfigForm extends Model
{
    protected $fillable = [
        'name',
        'config_id',
        'address',
        'active',
    ];
}
