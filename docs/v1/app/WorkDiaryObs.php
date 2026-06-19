<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WorkDiaryObs extends Model
{
    protected $fillable =[
        'sector',
        'description',
        'occurrence_id',
        'obs',
    ];
}
