<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkDiaryShiftTime extends Model
{
    protected $fillable = [
        'shift',
        'clear',
        'cloudy',
        'rain',
        'impractical'
    ];
}
