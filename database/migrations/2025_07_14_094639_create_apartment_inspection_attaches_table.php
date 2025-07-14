<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApartmentInspectionAttachesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('apartment_inspection_attaches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('apartment_inspection_id');
            $table->foreign('apartment_inspection_id')->references('id')->on('apartment_inspection_id');
            $table->string('name')->nullable();
            $table->string('attach')->nullable();
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
        Schema::dropIfExists('apartment_inspection_attaches');
    }
}
