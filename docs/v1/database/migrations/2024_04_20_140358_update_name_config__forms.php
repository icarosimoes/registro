<?php

use App\ConfigForm;
use Illuminate\Database\Migrations\Migration;


class UpdateNameConfigForms extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $form = ConfigForm::where('address','formulario.reuniao')->first();
        $form->name = 'Reuniões';
        $form->save(); 

        $form = ConfigForm::where('address','formulario.rel_turno')->first();
        $form->name = 'Relatório de Turno';
        $form->save(); 

        $form = ConfigForm::where('address','formulario.conf_suites')->first();
        $form->name = 'Conferências das Suites';
        $form->save(); 
        
        $form = ConfigForm::where('address','formulario.vist_suites')->first();
        $form->name = 'Vistorias das Suites';
        $form->save(); 

        $form = ConfigForm::where('address','formulario.diario_obras')->first();
        $form->name = 'Diário de Obras';
        $form->save(); 
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
