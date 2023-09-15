<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WorkDiaryActivity extends Model
{
    protected $fillable = [
        'sector',
        'description',
        'attachment',
        'team',
        'occurrence_id'
    ];
}
