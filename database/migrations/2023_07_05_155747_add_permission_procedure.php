<?php

use App\Models\Acl;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPermissionProcedure extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Acl::create(['controller' => 'ProcedureController', 'action' => 'update', 'name' => 'Editar Procedimento']);
        Acl::create(['controller' => 'ProcedureController', 'action' => 'store', 'name' => 'Criar Novo Procedimento']);
        Acl::create(['controller' => 'ProcedureController', 'action' => 'show', 'name' => 'Visualizar Procedimento']);
        Acl::create(['controller' => 'ProcedureController', 'action' => 'index', 'name' => 'Lista de Procedimento']);
        Acl::create(['controller' => 'ProcedureController', 'action' => 'delete', 'name' => 'Excluir Procedimento']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Acl::where('controller', 'ProcedureController')
        ->orWhere('action', 'update')
        ->orWhere('action', 'store')
        ->orWhere('action', 'view')
        ->orWhere('action', 'index')
        ->delete();
    }
}
