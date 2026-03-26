<?php

use App\ApartmentInspectionTypes;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLoftTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        ApartmentInspectionTypes::create([
            'name' => 'LOFT',
            
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('lofts', function (Blueprint $table) {
            $table->dropColumn('loft_type');
        });
    }
}
