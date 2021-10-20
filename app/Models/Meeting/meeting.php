<?php

namespace App\Models\Meeting;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class meeting extends Model
{
    public function users()
    {
        return $this->BelongsTo('App\Models\User');
    }
}
