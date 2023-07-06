<?php

use App\Models\Acl;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPermissionCheckSuite extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Acl::create(['controller' => 'CheckSuitesController', 'action' => 'update', 'name' => 'Editar Conferências de Suites']);
        Acl::create(['controller' => 'CheckSuitesController', 'action' => 'store', 'name' => 'Criar Nova Conferências de Suites']);
        Acl::create(['controller' => 'CheckSuitesController', 'action' => 'show', 'name' => 'Visualizar Conferências de Suites']);
        Acl::create(['controller' => 'CheckSuitesController', 'action' => 'index', 'name' => 'Lista de Conferências de Suites']);
        Acl::create(['controller' => 'CheckSuitesController', 'action' => 'delete', 'name' => 'Excluir Conferências de Suites']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Acl::where('controller', 'CheckSuitesController')
        ->orWhere('action', 'update')
        ->orWhere('action', 'store')
        ->orWhere('action', 'view')
        ->orWhere('action', 'index')
        ->delete();
    }
}
