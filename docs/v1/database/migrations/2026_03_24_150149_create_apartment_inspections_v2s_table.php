<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApartmentInspectionsV2sTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
 
    Schema::dropIfExists('apartment_inspections_v2s');
    
    Schema::create('apartment_inspections_v2s', function (Blueprint $table) {
      $table->id();
      $table->string('owner');
      $table->string('unit');
      $table->string('inspected_by');
      $table->date('inspection_date');
      $table->string('type_unit');
      $table->text('observation')->nullable();
      $table->string('approved', 3);
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
    Schema::dropIfExists('apartment_inspections_v2s');
  }
}
