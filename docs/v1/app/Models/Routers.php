<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Routers extends Model
{
    protected $fillable = [
        'name',
        'controller',
        'action',
        'module_id'
    ];

    public function module()
    {
        return $this->belongsTo('App\Models\Module');
    }
}
