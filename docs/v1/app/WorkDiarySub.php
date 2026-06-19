<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WorkDiarySub extends Model
{
    protected $fillable = [
        'company',
        'role',
        'total',
        'absent',
        'effective',
        'obs',
    ];
}
