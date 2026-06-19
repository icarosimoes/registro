<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WorkDiaryEquipament extends Model
{
    protected $fillable = [
        'supply',
        'description',
        'start',
        'end',
        'service',

    ];
}
