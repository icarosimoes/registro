<?php

namespace App\Services\Admin;

use App\Exceptions\ValidationException;
use App\Models\Acl;
use App\Models\Role;
use App\Models\Routers;
use App\Services\Service;
use App\Services\ServiceInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;

class PermissionService extends Service
{
    
    public function index()
    {
        $routers = Routers::all();
        return $routers;
    }

    public function show(int $id): Role 
    {
        return Acl::findOrFail($id);
    }

    public function store(Array $data, $id)
    {
        
        DB::beginTransaction();
        
        $role = Role::find($id);
        $acls = explode(",", $data['data']);
        $role->acl()->sync($acls); 
        
        DB::commit();
         // $pieces = explode(",", $comma_separated);
        // foreach ($pieces as $value) {
        //     $findRouter = $this->getRouters($value);
        //     $acl[] = array(
        //         'name' => $findRouter->name,
        //         'role_id' => $id,
        //         'controller' => $findRouter->controller,
        //         'action' => $findRouter->action,
        //         'module_id' => $findRouter->module_id
        //     );
        // }
        // $result = Acl::insert($acl);
        return true;
    }
    public function getRouters($id){
    $findRouter = Routers::findOrFail($id);
    return $findRouter;
    }
    public function getPermission($id)
    {
       $acls = Acl::where([['role_id', $id]])->get();
       return $acls; 
    }
    public function getModule($id)
    {
       $acl = Acl::findOrFail($id);
       return $acl; 
    }
    public function update(Array $data): Role
    {
        $this->validate($data); 
        $role = $this->show($data['id']);
        $role->name = $data['name'] ?? $role->name;
        $role->save();
        return $role;
    }

    public function destroy($id)
    {
        $acl = Acl::findOrFail($id);
        $acl->delete();
        return $acl;
    }

    public function restore($id)
    {
        $role = Role::withTrashed()->find($id);
        $role->restore();
        return $role;
    }

    private function validate(Array $data): bool
    {
        $validator = Validator::make(
            $data, 
            [
                'data' => 'required|max:255', 
            ],
            $this->getDefaultMessages()
        );

        if ($validator->fails()) {
            $e = new ValidationException('INVALID_DATA', 400);
            $e->setMessages($validator->errors()->getMessages());
            throw $e;
        }

        return $validator->fails();
    }
}