<?php

use App\Models\Acl;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPermissionInAcls extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Acl::create(['controller' => 'InspectionSuitesController', 'action' => 'update', 'name' => 'Editar Vistorias de Suites']);
        Acl::create(['controller' => 'InspectionSuitesController', 'action' => 'store', 'name' => 'Criar Nova Vistorias de Suites']);
        Acl::create(['controller' => 'InspectionSuitesController', 'action' => 'show', 'name' => 'Visualizar Vistorias de Suites']);
        Acl::create(['controller' => 'InspectionSuitesController', 'action' => 'index', 'name' => 'Lista de Vistorias de Suites']);
        Acl::create(['controller' => 'InspectionSuitesController', 'action' => 'delete', 'name' => 'Excluir Vistorias de Suites']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Acl::where('controller', 'InspectionSuitesController')
        ->orWhere('action', 'update')
        ->orWhere('action', 'store')
        ->orWhere('action', 'view')
        ->orWhere('action', 'index')
        ->delete();        
    }
}
