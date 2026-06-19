<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->tinyInteger('status')
                ->default('1')
                ->nullable()
                ->after('password');
            $table->unsignedBigInteger('role_id')
                ->nullable()
                ->after('status');
            $table->foreign('role_id')->references('id')->on('roles');
            $table->unsignedBigInteger('company_id')
                ->nullable()
                ->after('role_id');
            $table->foreign('company_id')->references('id')->on('companies');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['status','role_id','company_id']);
        });
    }
}
