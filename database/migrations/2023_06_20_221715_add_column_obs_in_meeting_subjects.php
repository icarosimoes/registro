<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnObsInMeetingSubjects extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('meeting_subjects', function (Blueprint $table) {
            $table->text('obs_subject')->nullable()->after('url_archive');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('meeting_subjects', function (Blueprint $table) {
            $table->dropColumn('obs_subject');
        });
    }
}
