<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MificarColunaOccupationInShiftReportFrequencies extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shift_report_frequencies', function (Blueprint $table) {
            $table->string('occupation')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shift_report_frequencies', function (Blueprint $table) {
            $table->string('occupation')->change();
        });
    }
}
