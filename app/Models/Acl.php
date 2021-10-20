<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Acl extends Model
{
    protected $fillable = [
        'controller', 'action', 'role_id', 'module_id'
    ];

    public function module()
    {
        return $this->belongsTo('App\Models\Module');
    }
}
