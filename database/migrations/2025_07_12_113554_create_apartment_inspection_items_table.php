<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApartmentInspectionItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('apartment_inspection_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('apartment_inspection_id');
            $table->foreign('apartment_inspection_id')->references('id')->on('apartment_inspections');
            $table->string('appreciation');
            $table->boolean('approved');
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
        Schema::dropIfExists('apartment_inspection_items');
    }
}
