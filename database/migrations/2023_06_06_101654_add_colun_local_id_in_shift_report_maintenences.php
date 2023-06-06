<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColunLocalIdInShiftReportMaintenences extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shift_report_maintenences', function (Blueprint $table) {
            $table->foreignId('local_id')->nullable()->after('shift_reports_id');
            $table->string('uh')->nullable()->change();
            $table->foreign('local_id')->references('id')->on('locals');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shift_report_maintenences', function (Blueprint $table) {
            $table->dropForeign(['local_id']);
            $table->dropColumn('local_id');
            $table->string('uh')->change();
        });
    }
}
