<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
class WorkDiary extends Model
{
    use SoftDeletes;

    public function work_diary_frequency_adm(){
        return $this->hasMany(WorkDiaryFrequencyAdm::class);
    }

    public function work_diary_frequency_prod(){
        return $this->hasMany(WorkDiaryFrequencyProd::class);
    }

    public function work_diary_sub(){
        return $this->hasMany(WorkDiarySub::class);
    }
    //
}
