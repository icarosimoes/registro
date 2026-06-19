<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEquipamentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('work_diary_equipaments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('work_diary_id');
            $table->string('supply');
            $table->string('description');
            $table->date('start');
            $table->date('end');
            $table->string('service');
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
        Schema::table('work_diary_equipaments', function (Blueprint $table) {
            $table->dropForeign(['work_diary_id']);
        });
        
        Schema::dropIfExists('work_diary_equipaments');
    }
}
