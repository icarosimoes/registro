<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveApartement extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('audit_reports', function (Blueprint $table) {
            $table->dropColumn('apartment_maintenance');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('audit_reports', function (Blueprint $table) {
            $table->string('apartment_maintenance')->nullable()->after('uh');
        });
    }
}
