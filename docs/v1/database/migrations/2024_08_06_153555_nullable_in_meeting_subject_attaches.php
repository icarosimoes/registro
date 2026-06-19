<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class NullableInMeetingSubjectAttaches extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('meeting_subject_attaches', function (Blueprint $table) {
            $table->foreignId('meeting_subject_id')->nullable()->change();
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
            $table->foreignId('meeting_subject_id')->change();
        });
    }
}
