<?php

use App\Models\Acl;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPermissionSector extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Acl::create(['controller' => 'SectorController', 'action' => 'update', 'name' => 'Editar Departamento']);
        Acl::create(['controller' => 'SectorController', 'action' => 'store', 'name' => 'Criar Novo Departamento']);
        Acl::create(['controller' => 'SectorController', 'action' => 'show', 'name' => 'Visualizar Departamento']);
        Acl::create(['controller' => 'SectorController', 'action' => 'index', 'name' => 'Lista de Departamento']);
        Acl::create(['controller' => 'SectorController', 'action' => 'delete', 'name' => 'Excluir Departamento']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Acl::where('controller', 'SectorController')
            ->orWhere('action', 'update')
            ->orWhere('action', 'store')
            ->orWhere('action', 'view')
            ->orWhere('action', 'index')
            ->delete();
    }
}
