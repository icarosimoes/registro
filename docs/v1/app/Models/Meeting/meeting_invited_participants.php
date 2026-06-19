<?php

namespace App\Models\Meeting;

use Illuminate\Database\Eloquent\Model;

class meeting_invited_participants extends Model
{
   public function participants()
   {
       return $this->belongsTo('App\Models\Meeting\participants');
   }
}
