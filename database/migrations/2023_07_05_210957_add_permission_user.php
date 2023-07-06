<?php

use App\Models\Acl;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPermissionUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Acl::create(['controller' => 'UserController', 'action' => 'update', 'name' => 'Editar Usuário']);
        Acl::create(['controller' => 'UserController', 'action' => 'store', 'name' => 'Criar Novo Usuário']);
        Acl::create(['controller' => 'UserController', 'action' => 'show', 'name' => 'Visualizar Usuário']);
        Acl::create(['controller' => 'UserController', 'action' => 'index', 'name' => 'Lista de Usuário']);
        Acl::create(['controller' => 'UserController', 'action' => 'delete', 'name' => 'Excluir Usuário']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Acl::where('controller', 'UserController')
        ->orWhere('action', 'update')
        ->orWhere('action', 'store')
        ->orWhere('action', 'view')
        ->orWhere('action', 'index')
        ->delete();
    }
}
