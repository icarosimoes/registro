<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TypeUnitInApartmentInspections extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('apartment_inspections', function (Blueprint $table) {
            $table->string('type_unit')->nullable()->after('inspection_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('apartment_inspections', function (Blueprint $table) {
            $table->dropColumn('type_unit');
        });
    }
}
