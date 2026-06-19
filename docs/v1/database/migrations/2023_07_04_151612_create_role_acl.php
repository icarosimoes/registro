<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRoleAcl extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('role_acl', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id');
            $table->foreignId('acl_id');
            $table->timestamps();
            $table->foreign('role_id')->references('id')->on('roles');
            $table->foreign('acl_id')->references('id')->on('acls');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('role_acl');
    }
}
