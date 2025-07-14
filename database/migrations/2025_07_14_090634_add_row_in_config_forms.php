<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddRowInConfigForms extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
            DB::table('config_forms')->insert([
                'config_id'=>'1',
                'name'=>'Vistoria de Apartamento',
                'address'=>'formulario.vistoria_apartamentos',
                'active' =>'yes',
            ]);
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('config_forms')->where('name','Vistoria de Apartamento')->delete();
    }
}
