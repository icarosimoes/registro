<?php

use App\Models\Acl;
use App\Models\Role;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AssocAdminAllPermission extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $assocs = DB::table('role_acl')->delete();
        $role = Role::first();  
        $acls = Acl::all();   
        foreach($acls as $acl){
          $result = DB::table('role_acl')->where('role_id', $role->id )->where('acl_id', $acl)->first(); //verifica se ja existe essa associacao
          if(!$result){
              $role->acl()->attach($acl); 
          }
       }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $assocs = DB::table('role_acl')->delete();
        
    }
}
