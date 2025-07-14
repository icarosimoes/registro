<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApartmentInspectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('apartment_inspections', function (Blueprint $table) {
            $table->id();
            $table->string('owner');
            $table->string('unit');
            $table->string('inspected_by');
            $table->dateTime('inspection_date');
            $table->text('observation')->nullable();
            $table->string('approved',3)->nullable();
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
        Schema::dropIfExists('apartment_inspections');
    }
}
