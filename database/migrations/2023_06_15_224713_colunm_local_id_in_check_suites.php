<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ColunmLocalIdInCheckSuites extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('check_suites', function (Blueprint $table) {
            $table->dropColumn('suite');
            $table->dropColumn('inspected_by');
            $table->foreignId('local_id')->after('date')->nullable();
            $table->foreignId('user_id')->after('local_id')->nullable();
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
        Schema::table('check_suites', function (Blueprint $table) {
            $table->dropForeign(['local_id']);
            $table->dropForeign(['user_id']);
            $table->dropColumn('local_id');
            $table->dropColumn('user_id');
            $table->string('suite');
            $table->string('inspected_by');
            
        });
    }
}
