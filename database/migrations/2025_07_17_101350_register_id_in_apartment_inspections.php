<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RegisterIdInApartmentInspections extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('apartment_inspection_items', function (Blueprint $table) {
            $table->foreignId('occurrence_id')->nullable()->after('approved');
            $table->foreign('occurrence_id')->references('id')->on('occurrences');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('apartment_inspection_items', function (Blueprint $table) {
            $table->dropForeign(['occurrence_id']);
            $table->dropColumn(['occurrence_id']);
        });
    }
}
