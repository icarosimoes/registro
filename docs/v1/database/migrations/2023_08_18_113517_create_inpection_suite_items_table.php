<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInpectionSuiteItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inspection_suite_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inspection_suite_id');
            $table->foreignId('occurrences_id')->nullable();
            $table->integer('item');
            $table->string('valuation',3);
            $table->string('register')->nullable();
            $table->timestamps();
            $table->foreign('inspection_suite_id')->references('id')->on('inspection_suites');
            $table->foreign('occurrences_id')->references('id')->on('occurrences');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('inspection_suite_items', function (Blueprint $table) {
            $table->dropForeign(['inspection_suite_id']);
            $table->dropForeign(['occurrences_id']);
            $table->dropColumn('occurrences_id');
            $table->dropColumn('inspection_suite_id');
        });
        Schema::dropIfExists('inspection_suite_items');
    }
}
