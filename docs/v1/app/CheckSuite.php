<?php

namespace App;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class CheckSuite extends Model
{
    use SoftDeletes;
    
    public function check_suite_items()
    {
        return $this->hasMany(CheckSuiteItem::class, 'check_suite_id');
    }
    
    public function local()
    {
        return $this->belongsTo(Local::class,'local_id');
    }
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
