<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCheckSuiteItems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('check_suite_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('check_suite_id');
            $table->foreignId('occurrences_id')->nullable();
            $table->integer('item');
            $table->string('valuation',3);
            $table->string('register')->nullable();
            $table->timestamps();
            $table->foreign('occurrences_id')->references('id')->on('occurrences');
            $table->foreign('check_suite_id')->references('id')->on('check_suites');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('check_suite_items');
    }
}
