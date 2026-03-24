<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApartmentInspectionItemsV2sTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('apartment_inspection_items_v2s', function (Blueprint $table) {
            $table->id();
            $table->foreignId('apartment_inspection_id');
            $table->foreign('apartment_inspection_id')->references('id')->on('apartment_inspections_v2s');
            $table->string('appreciation')->nullable();
            $table->string('approved', 3);
            $table->unsignedBigInteger('occurrence_id')->nullable();
            $table->foreign('occurrence_id')->references('id')->on('occurrences');
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
        Schema::dropIfExists('apartment_inspection_items_v2s');
    }
}
