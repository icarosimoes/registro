<?php

namespace App\Models\Meeting;

use Illuminate\Database\Eloquent\Model;

class meeting_registered_participants extends Model
{
    public function users()
   {
       return $this->belongsTo('App\Models\User');
   }
}
