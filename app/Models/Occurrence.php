<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Occurrence extends Model
{
    public function type_occurrences(){
        return $this->belongsTo('App\Models\TypeOccurrence');
    }
    public function users(){
        return $this->belongsTo('App\Models\User');
    }
}
