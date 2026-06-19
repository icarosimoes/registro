<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMeetingSubjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('meeting_subjects', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('meetings_id');
            $table->foreign('meetings_id')->references('id')->on('meetings');
            $table->string('subject');
            $table->string('url_archive')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('meeting_subjects');
    }
}
