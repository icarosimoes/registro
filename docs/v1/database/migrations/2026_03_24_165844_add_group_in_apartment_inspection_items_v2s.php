<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGroupInApartmentInspectionItemsV2s extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('apartment_inspection_items_v2s', function (Blueprint $table) {
            $table->string('group')->nullable()->after('apartment_inspection_id');
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
            $table->dropColumn('group');
        });
    }
}
