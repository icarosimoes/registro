<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class NewSubjectIdInMeetingSubjectAttaches extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('meeting_subject_attaches', function (Blueprint $table) {
            $table->foreignId('meeting_new_subject_id')->nullable()->after('meeting_subject_id');
            $table->foreign('meeting_new_subject_id')->references('id')->on('meeting_new_subjects');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('meeting_subject_attaches', function (Blueprint $table) {
            $table->dropForeign(['meeting_new_subject_id']);
            $table->dropColumn('meeting_new_subject_id');
        });
    }
}
