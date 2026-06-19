<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WorkDiaryFrequencyProd extends Model
{
    protected $fillable = [
        'work_diary_id',
        'role',
        'total',
        'absent',
        'effective',
        'obs',
    ];
}
