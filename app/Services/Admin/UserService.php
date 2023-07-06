<?php

namespace App\Services\Admin;

use App\Exceptions\ValidationException;
use App\Models\User;
use App\Services\Service;
use App\Services\ServiceInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;

class UserService extends Service 
{
    private $searchables = ['name', 'email', 'password'];
    private $sortables = ['name', 'email', 'password'];
    private $conditionables = ['name', 'email', 'password'];

    public function index(Array $data)
    {
        $users = User::query();

        // Pagination
        $limit = !empty($data['limit']) ? $data['limit'] : Config::get('variables.perPage');
        $maxPerPage = Config::get('variables.maxPerPage');
        $limit = ($limit <= $maxPerPage) ? $limit : $maxPerPage;

        // Orders
        if (!empty($data['orders'])){
            $orders = $data['orders'];
            foreach ($orders as $field => $order) {
                if (in_array($field, $this->sortables)) {
                    $users = $users->orderBy($field, $order);
                }
            }
        }

        // Searches
        if (!empty($data['searches'])) {
            $filters = $data['searches'];
            foreach ($filters as $field => $value) {
                if (in_array($field, $this->searchables)) {
                    $users = $users->where($field, 'like', '%'.$value.'%');
                }
            }
        }

        // Conditions
        if (!empty($data['conditions'])) {
            $filters = $data['conditions'];
            foreach ($filters as $field => $value) {
                if (in_array($field, $this->conditionables)) {
                    $value = explode(',', $value);
                    $users = $users->whereIN($field, $value);
                }
            }
        }

        return $users->paginate($limit);
    }

    public function show(int $id): User 
    {
        return User::findOrFail($id);
    }

    public function store(Array $data)
    {
        
        $this->validate($data);
        if ($data['photo'] != 'undefined') {
            $path = $data['photo']->store('images');
        } else {
            $path = 'images/avatarDefaultBpd2020.jpg';
        }
        $resultEmail = $this->duplicateEmail($data['email']);
        if ($resultEmail == true) {
            return false;
        }else{
            $user = new User();
            $user->name  = $data['name'];
            $user->email = $data['email']; 
            $user->role_id = $data['profile'];
            $user->image = $path;
            $user->password = Hash::make($data['password']);
            $user->save();
            return $user;
        }
    }
    public function getProfile(){
        $profile = DB::table('roles')->select('id', 'name')->get();
        return $profile;
    }
    public function getUser()
    {
        $users = User::all();
        return $users;
    }

    public function duplicateEmail($email)
    {
        $result = User::where('email', $email)->first();
        if ($result) {
            return true;
        }
    }

    public function update(Array $data): User
    {
        $this->validateUpdate($data); 
        
        $user = $this->show($data['userId']);

        $user->name = $data['name'] ?? $user->name;
        $user->email = $data['email'] ?? $user->email;
        $user->role_id = $data['profile'] ?? $user->role_id;
        if(!empty($data['password'])){
          $user->password = Hash::make($data['password']) ?? $user->password;
        }
		
        $user->save();

        return $user;
    }

    public function updateImage(Array $data)
    {
        $path = $data['image']->store('images');
        $user = $this->show($data['userId']);
        $user->image = $path ?? $user->image;
        $user->save();
        return $user;
    }

    public function destroy($id)
    {
        $user = $this->show($id);
        $user->delete();
        return $user;
    }

    public function restore($id)
    {
        $user = User::withTrashed()->find($id);
        $user->restore();
        return $user;
    }

    private function validate(Array $data): bool
    {
        $validator = Validator::make(
            $data, 
            [
                'name' => 'required|max:255', 
				'email' => 'required|email|max:255', 
                'password' => 'required|max:255',
                'profile' => 'required', 
				
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
    private function validateUpdate(Array $data): bool
    {
        $validator = Validator::make(
            $data, 
            [
                'name' => 'required|max:255', 
				'email' => 'required|email|max:255', 
                'profile' => 'required', 
				
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