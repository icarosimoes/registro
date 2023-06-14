<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CheckSuite extends Model
{
    public function check_suite_items()
    {
        return $this->hasMany(CheckSuiteItem::class, 'check_suite_id');
    }
}
