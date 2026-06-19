<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddServiceInApartmentInspectionItemsV2s extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('apartment_inspection_items_v2s', function (Blueprint $table) {
              $table->string('service')->nullable()->after('apartment_inspection_id');
              $table->string('item_verification')->nullable()->after('service');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('apartment_inspection_items_v2s', function (Blueprint $table) {
            $table->dropColumn('service');
            $table->dropColumn('item_verification');
        });
    }
}
