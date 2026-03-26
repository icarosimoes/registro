<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApartamentInspectionItemAttachesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('apartament_inspection_item_attaches');
        
        Schema::create('apartament_inspection_item_attaches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('apartment_item_id');
            $table->foreign('apartment_item_id')->references('id')->on('apartment_items_v2s');
            $table->string('name')->nullable();
            $table->string('attach')->nullable();
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
        Schema::dropIfExists('apartament_inspection_item_attaches');
    }
}
