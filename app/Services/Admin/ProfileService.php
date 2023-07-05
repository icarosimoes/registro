<?php

namespace App\Services\Admin;

use App\Exceptions\ValidationException;
use App\Models\Role;
use App\Services\Service;
use App\Services\ServiceInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;

class ProfileService extends Service
{
    
    public function index()
    {
        $roles = Role::all();
        return $roles;
    }

    public function show(int $id): Role 
    {
        return Role::findOrFail($id);
    }

    public function store(Array $data)
    {
        $this->validate($data);
        
            $role = new Role();
            $role->name  = $data['name'];
            $role->save();
            return $role;
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
        $role = $this->show($id);
        $role->delete();
        return $role;
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
                'name' => 'required|max:255', 
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