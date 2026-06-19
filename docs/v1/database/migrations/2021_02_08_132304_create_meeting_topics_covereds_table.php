<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMeetingTopicsCoveredsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('meeting_topics_covereds', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('meetings_id');
            $table->foreign('meetings_id')->references('id')->on('meetings');
            $table->string('subject_addressed');
            $table->text('providence')->nullable();
            $table->unsignedBigInteger('occurrences_id')->nullable();
            $table->foreign('occurrences_id')->references('id')->on('occurrences');
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
        Schema::dropIfExists('meeting_topics_covereds');
    }
}
