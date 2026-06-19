<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAuditReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('audit_reports', function (Blueprint $table) {
            $table->id();
            $table->string('date');
            $table->float('occupation')->nullable();
            $table->float('average_daily')->nullable();
            $table->integer('guests')->nullable();
            $table->integer('uh')->nullable();
            $table->integer('maintenance_apartment')->nullable();
            $table->integer('cleaning')->nullable();
            $table->text('apartment_maintenance')->nullable();
            $table->text('walk_in')->nullable();
            $table->text('obs')->nullable();
            $table->text('A&B')->nullable();
            $table->text('reception')->nullable();
            $table->text('reservations')->nullable();
            $table->text('housekeeping')->nullable();
            $table->text('maintenance')->nullable();
            $table->text('ti')->nullable();
            $table->text('security')->nullable();
            $table->foreignId('user_id')->constrained(); 
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
        Schema::dropIfExists('audit_reports');
    }
}
