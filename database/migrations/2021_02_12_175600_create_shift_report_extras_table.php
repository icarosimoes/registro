<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShiftReportExtrasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shift_report_extras', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shift_reports_id');
            $table->foreign('shift_reports_id')->references('id')->on('shift_reports');
            $table->string('extrawork');
            $table->string('reasons');
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
        Schema::dropIfExists('shift_report_extras');
    }
}
