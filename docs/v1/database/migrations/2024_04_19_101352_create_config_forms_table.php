<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConfigFormsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('config_forms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('config_id');
            $table->string('address')->nullable();
            $table->string('active',3)->default('yes'); 
            $table->foreign('config_id')->references('id')->on('configs');
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
        Schema::dropIfExists('config_forms');
    }
}
