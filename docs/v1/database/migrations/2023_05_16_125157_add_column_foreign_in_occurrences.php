<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnForeignInOccurrences extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('occurrences', function (Blueprint $table) {
            $table->foreignId('local_id')->nullable()->after('id');
            $table->foreignId('sector_id')->nullable()->after('local_id');
            $table->foreign('local_id')->references('id')->on('locals');
            $table->foreign('sector_id')->references('id')->on('sectors');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('occurrences', function (Blueprint $table) {
            $table->dropForeign(['local_id']);
            $table->dropForeign(['sector_id']);
        });
    }
}
