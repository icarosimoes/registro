<?php

namespace App\Models\ShiftReport;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShiftReport extends Model
{
    public function users(){
        return $this->BelongsTo('App\Models\User');
    }
}
