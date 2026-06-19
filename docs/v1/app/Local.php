<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Local extends Model
{
    use SoftDeletes;

    public function scopeSelectSearch($query, $request)
    {
        preg_match('/\d+/', $request->term, $match3);

        if (!empty($match3)) {
            $code = $match3[0];
            return $query->where('id', 'like', "%$code%");
        } else {
            if ($request->term != null) {
                return $query->where('name', 'like', "%$request->term%");
            }
        }
    }
}
