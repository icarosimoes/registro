<?php

use App\Models\Acl;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPermissionLocal extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Acl::create(['controller' => 'LocalController', 'action' => 'update', 'name' => 'Editar Local']);
        Acl::create(['controller' => 'LocalController', 'action' => 'store', 'name' => 'Criar Novo Local']);
        Acl::create(['controller' => 'LocalController', 'action' => 'show', 'name' => 'Visualizar Local']);
        Acl::create(['controller' => 'LocalController', 'action' => 'index', 'name' => 'Lista de Local']);
        Acl::create(['controller' => 'LocalController', 'action' => 'delete', 'name' => 'Excluir Local']);
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
