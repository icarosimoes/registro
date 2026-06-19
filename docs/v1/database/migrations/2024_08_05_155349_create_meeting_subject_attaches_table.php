<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMeetingSubjectAttachesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('meeting_subject_attaches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('meeting_subject_id');
            $table->string('description')->nullable();
            $table->string('dir')->nullable();
            $table->foreign('meeting_subject_id')->references('id')->on('meeting_subjects');
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
        Schema::dropIfExists('meeting_subject_attaches');
    }
}
