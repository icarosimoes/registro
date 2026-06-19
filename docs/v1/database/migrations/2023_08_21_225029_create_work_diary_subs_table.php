<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWorkDiarySubsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('work_diary_subs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('work_diary_id');
            $table->string('company');
            $table->string('role');
            $table->integer('total')->default('0');
            $table->integer('absent')->default('0');
            $table->integer('effective')->default('0');
            $table->text('obs')->nullable();
            $table->timestamps();
            $table->foreign('work_diary_id')->references('id')->on('work_diaries');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('work_diary_subs', function (Blueprint $table) {
            $table->dropForeign(['work_diary_id']);
        });
        Schema::dropIfExists('work_diary_subs');
    }
}
