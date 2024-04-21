<?php

use App\Config;
use App\ConfigForm;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class PrimerosDadosConfigForms extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $config =Config::first();
        $configForm = new ConfigForm(); 
        $configForm->config_id = $config->id;
        $configForm->address = 'formulario.reuniao';
        $configForm->active = 'yes';
        $configForm->save();

        
        $configForm = new ConfigForm(); 
        $configForm->config_id = $config->id;
        $configForm->address = 'formulario.rel_turno';
        $configForm->active = 'yes';
        $configForm->save();

        
        $configForm = new ConfigForm(); 
        $configForm->config_id = $config->id;
        $configForm->address = 'formulario.conf_suites';
        $configForm->active = 'yes';
        $configForm->save();

        
        $configForm = new ConfigForm(); 
        $configForm->config_id = $config->id;
        $configForm->address = 'formulario.vist_suites';
        $configForm->active = 'yes';
        $configForm->save();

        
        $configForm = new ConfigForm(); 
        $configForm->config_id = $config->id;
        $configForm->address = 'formulario.diario_obras';
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
        $config =Config::first();
        Config::where('config_id',$config->id)->delete();
    }
}
