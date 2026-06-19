<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMeetingInvitedParticipantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('meeting_invited_participants', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('meetings_id');
            $table->foreign('meetings_id')->references('id')->on('meetings');
            $table->unsignedBigInteger('participants_id');
            $table->foreign('participants_id')->references('id')->on('participants');
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
        Schema::dropIfExists('meeting_invited_participants');
    }
}
