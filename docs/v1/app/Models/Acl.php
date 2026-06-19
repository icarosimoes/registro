<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Acl extends Model
{
    protected $fillable = [
        'controller', 'action', 'name'
    ];

    public function module()
    {
        return $this->belongsTo('App\Models\Module');
    }
}
