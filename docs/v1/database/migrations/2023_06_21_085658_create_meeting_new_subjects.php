<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMeetingNewSubjects extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('meeting_new_subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('meetings_id');
            $table->string('subject')->nullable(); 
            $table->string('url_archive')->nullable(); 
            $table->text('obs_subject')->nullable(); 
            $table->timestamps();
            $table->foreign('meetings_id')->references('id')->on('meetings');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('meeting_new_subjects');
    }
}
