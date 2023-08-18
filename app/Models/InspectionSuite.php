<?php

namespace App\Models;

use App\Local;
use Illuminate\Database\Eloquent\Model;

class InspectionSuite extends Model
{
    public function local()
    {
        return $this->belongsTo(Local::class,'local_id');
    }
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function inspection_suite_items(){
        return $this->hasMany(InspectionSuiteItem::class);
    }
}
