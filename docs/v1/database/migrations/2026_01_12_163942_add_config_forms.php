<?php

use App\ConfigForm;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddConfigForms extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        ConfigForm::create([
            'name' => 'Relatório de Auditoria',
            'config_id' => '1',
            'address' => 'formulario.rel_auditoria',
            'active' => 'yes',
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        ConfigForm::where('address', 'formulario.rel_auditoria')->delete();
    }
}
