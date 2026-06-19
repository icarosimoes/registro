<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWorkDiaryObsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('work_diary_obs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('work_diary_id');
            $table->string('sector');    
            $table->string('description');
            $table->string('register');     
            $table->string('obs')->nullable();
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
        Schema::table('work_diary_obs', function (Blueprint $table) {
            $table->dropForeign(['work_diary_id']);
        });

        Schema::dropIfExists('work_diary_obs');
    }
}
