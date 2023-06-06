<?php

namespace App\Models\ShiftReport;

use App\Local;
use Illuminate\Database\Eloquent\Model;

class ShiftReport_maintenence extends Model
{
    function local(){
        return $this->belongsTo(Local::class);
    }
}
