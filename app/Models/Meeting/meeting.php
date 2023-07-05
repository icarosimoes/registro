<?php

namespace App\Models\Meeting;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
class meeting extends Model
{
    use SoftDeletes;

    public function users()
    {
        return $this->BelongsTo('App\Models\User')->withTrashed();
    }
}
