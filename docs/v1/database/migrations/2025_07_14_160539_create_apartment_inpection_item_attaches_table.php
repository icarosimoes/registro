<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApartmentInpectionItemAttachesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('apartment_inpection_item_attaches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('apartment_item_id');
            $table->foreign('apartment_item_id')->references('id')->on('apartment_inspection_items');
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
        Schema::dropIfExists('apartment_inpection_item_attaches');
    }
}
