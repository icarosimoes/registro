<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Occurrence_comments extends Model
{
    public function users(){
        return $this->belongsTo('App\Models\User');
    }
}
