<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnAcurrenceIdInWorkDiaryActivities extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('work_diary_activities', function (Blueprint $table) {
            $table->foreignId('occurrence_id')->nullable()->after('work_diary_id');
            $table->foreign('occurrence_id')->references('id')->on('occurrences');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('work_diary_activities', function (Blueprint $table) {
            $table->dropForeign(['occurrence_id']);
            $table->dropColumn('occurrence_id');
        });
    }
}
