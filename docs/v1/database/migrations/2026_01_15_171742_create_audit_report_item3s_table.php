<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAuditReportItem3sTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('audit_report_item3s', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('audit_report_id');
            $table->foreign('audit_report_id')->references('id')->on('audit_reports');
            $table->string('reserve');
            $table->string('name');
            $table->string('pax');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('audit_report_item3s');
    }
}
