<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeAb extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('audit_reports', function (Blueprint $table) {
            $table->dropColumn('A&B');
            $table->string('AB', 255)->nullable()->after('obs');
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
            $table->dropColumn('AB');
            $table->string('A&B', 255)->nullable()->after('obs');
        });
    }
}
