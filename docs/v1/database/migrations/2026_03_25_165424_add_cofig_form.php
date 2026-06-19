<?php

use App\ConfigForm;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCofigForm extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $configForm = new ConfigForm();
        $configForm->config_id = 1;
        $configForm->name = 'Vistoria de Apartamento v2';
        $configForm->address = 'formulario.vistoria_apartamentos_v2';
        $configForm->active = 'yes';

        $configForm->save();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $configForm = ConfigForm::where('address', 'formulario.vistoria_apartamentos_v2')->first();
        if ($configForm) {
            $configForm->delete();
        }
    }
}
