<?php

use App\Models\Acl;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPermissionShifitReport extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Acl::create(['controller' => 'ShifitReportController', 'action' => 'update', 'name' => 'Editar Relatório de Turno']);
        Acl::create(['controller' => 'ShifitReportController', 'action' => 'store', 'name' => 'Criar Novo Relatório de Turno']);
        Acl::create(['controller' => 'ShifitReportController', 'action' => 'show', 'name' => 'Visualizar Relatório de Turno']);
        Acl::create(['controller' => 'ShifitReportController', 'action' => 'index', 'name' => 'Lista de Relatório de Turno']);
        Acl::create(['controller' => 'ShifitReportController', 'action' => 'delete', 'name' => 'Excluir Relatório de Turno']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Acl::where('controller', 'ShifitReportController')
        ->orWhere('action', 'update')
        ->orWhere('action', 'store')
        ->orWhere('action', 'view')
        ->orWhere('action', 'index')
        ->delete();
    }
}
