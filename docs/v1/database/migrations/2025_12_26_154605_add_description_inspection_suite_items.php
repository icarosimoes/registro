<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDescriptionInspectionSuiteItems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('inspection_suite_items', function (Blueprint $table) {
            $table->text('description')->nullable()->after('occurrences_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('inspection_suite_items', function (Blueprint $table) {
            $table->dropColumn('description');
        });
    }
}
