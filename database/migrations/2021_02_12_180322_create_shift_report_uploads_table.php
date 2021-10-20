<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShiftReportUploadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shift_report_uploads', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shift_reports_id');
            $table->foreign('shift_reports_id')->references('id')->on('shift_reports');
            $table->string('url_upload');
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
        Schema::dropIfExists('shift_report_uploads');
    }
}
