<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShiftReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shift_reports', function (Blueprint $table) {
            $table->id();
            $table->dateTime('beginning');
            $table->dateTime('end');
            $table->string('supervisor');
            $table->integer('return_of_customers')->nullable();
            $table->integer('inputQuantity');
            $table->integer('outputQuantity');
            $table->unsignedBigInteger('users_id');
            $table->foreign('users_id')->references('id')->on('users');
            $table->integer('status')->default(1);
            $table->integer('viewed')->nullable();
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
        Schema::dropIfExists('shift_reports');
    }
}
