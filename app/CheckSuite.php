<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class CheckSuite extends Model
{
    use SoftDeletes;
    
    public function check_suite_items()
    {
        return $this->hasMany(CheckSuiteItem::class, 'check_suite_id');
    }
}
