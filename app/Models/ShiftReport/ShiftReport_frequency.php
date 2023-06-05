<?php

namespace App\Models\ShiftReport;

use App\Func;
use Illuminate\Database\Eloquent\Model;

class ShiftReport_frequency extends Model
{
    public function func (){
        return $this->belongsTo(Func::class);
    } 
}
