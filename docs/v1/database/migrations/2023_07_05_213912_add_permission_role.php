<?php

use App\Models\Acl;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPermissionRole extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Acl::create(['controller' => 'ProfileController', 'action' => 'update', 'name' => 'Editar Perfil']);
        Acl::create(['controller' => 'ProfileController', 'action' => 'store', 'name' => 'Criar Novo Perfil']);
        Acl::create(['controller' => 'ProfileController', 'action' => 'show', 'name' => 'Visualizar Perfil']);
        Acl::create(['controller' => 'ProfileController', 'action' => 'index', 'name' => 'Lista de Perfil']);
        Acl::create(['controller' => 'ProfileController', 'action' => 'delete', 'name' => 'Excluir Perfil']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Acl::where('controller', 'ProfileController')
        ->orWhere('action', 'update')
        ->orWhere('action', 'store')
        ->orWhere('action', 'view')
        ->orWhere('action', 'index')
        ->delete();
    }
}
