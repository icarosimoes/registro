<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInspectionSuitesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inspection_suites', function (Blueprint $table) {
            $table->id();
            $table->dateTime('date');
            $table->foreignId('local_id')->nullable(); 
            $table->foreignId('user_id')->nullable(); 
            $table->text('obs')->nullable();
            $table->string('status',15);
            $table->string('maid')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('local_id')->references('id')->on('locals');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('inspection_suites', function (Blueprint $table) {
            $table->dropForeign(['local_id']);
            $table->dropForeign(['user_id']);
            $table->dropColumn('local_id');
            $table->dropColumn('user_id');

        });
        Schema::dropIfExists('inspection_suites');
    }
}
