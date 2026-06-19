<?php

use App\Models\Acl;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPermissionRegister extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Acl::create(['controller' => 'OccurrenceController', 'action' => 'update', 'name' => 'Editar Registro']);
        Acl::create(['controller' => 'OccurrenceController', 'action' => 'store', 'name' => 'Criar Novo Registro']);
        Acl::create(['controller' => 'OccurrenceController', 'action' => 'show', 'name' => 'Visualizar Registro']);
        Acl::create(['controller' => 'OccurrenceController', 'action' => 'index', 'name' => 'Lista de Registro']);
        Acl::create(['controller' => 'OccurrenceController', 'action' => 'delete', 'name' => 'Excluir Registro']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Acl::where('controller', 'OccurrenceController')
        ->orWhere('action', 'update')
        ->orWhere('action', 'store')
        ->orWhere('action', 'view')
        ->orWhere('action', 'index')
        ->delete();
    }
}
