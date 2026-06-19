<?php

use App\Models\Acl;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPermissionFunction extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Acl::create(['controller' => 'FunctionController', 'action' => 'update', 'name' => 'Editar Função']);
        Acl::create(['controller' => 'FunctionController', 'action' => 'store', 'name' => 'Criar Nova Função']);
        Acl::create(['controller' => 'FunctionController', 'action' => 'show', 'name' => 'Visualizar Função']);
        Acl::create(['controller' => 'FunctionController', 'action' => 'index', 'name' => 'Lista de Função']);
        Acl::create(['controller' => 'FunctionController', 'action' => 'delete', 'name' => 'Excluir Função']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Acl::where('controller', 'LocalController')
            ->orWhere('action', 'update')
            ->orWhere('action', 'store')
            ->orWhere('action', 'view')
            ->orWhere('action', 'index')
            ->delete();
    }
}
