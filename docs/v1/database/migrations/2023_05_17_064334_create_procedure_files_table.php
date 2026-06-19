<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProcedureFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('procedure_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('procedure_id');
            $table->string('name');
            $table->string('file');
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('procedure_id')->references('id')->on('procedures');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('procedure_files', function (Blueprint $table) {
            $table->dropForeign(['procedure_id']);
        });

        Schema::dropIfExists('procedure_files');
    }
}
