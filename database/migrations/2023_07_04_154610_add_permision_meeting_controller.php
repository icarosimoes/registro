<?php

use App\Models\Acl;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPermisionMeetingController extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Acl::create(['controller' => 'MeetingController', 'action' => 'update', 'name' => 'Editar Reunião']);
        Acl::create(['controller' => 'MeetingController', 'action' => 'store', 'name' => 'Criar Nova Reunião']);
        Acl::create(['controller' => 'MeetingController', 'action' => 'view', 'name' => 'Visualizar Reunião']);
        Acl::create(['controller' => 'MeetingController', 'action' => 'index', 'name' => 'Lista de Reunião']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Acl::where('controller', 'MeetingController')
            ->orWhere('action', 'update')
            ->orWhere('action', 'store')
            ->orWhere('action', 'view')
            ->orWhere('action', 'index')
            ->delete();
    }
}
