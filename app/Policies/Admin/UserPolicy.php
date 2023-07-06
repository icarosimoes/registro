<?php

namespace App\Policies\Admin;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Auth;

class UserPolicy
{
    use HandlesAuthorization;

    public const CONTROLLER = 'UserController';
    /**
    * Verifica se tem a permissao.
    */
   private function hasPermission($action){
       return  Auth::user()->role->acl()
         ->where('controller', self::CONTROLLER)
         ->where('action', $action)
         ->first();
     }
 
     /**
      * Determine whether the user can view any models.
      *
      * @param  \App\Models\User  $user
      * @return mixed
      */
     public function index(User $user)
     {
         if ($this->hasPermission('index')) {
             return true;
         }
         return false;
     }
 
     /**
      * Determine whether the user can view the model.
      *
      * @param  \App\Models\User  $user
      * @param  \App\Meeting  $meeting
      * @return mixed
      */
     public function show(User $user )
     {
         if ($this->hasPermission('show')) {
             return true;
         }
         return false;
     }
 
     /**
      * Determine whether the user can create models.
      *
      * @param  \App\Models\User  $user
      * @return mixed
      */
     public function store(User $user)
     {
       if ($this->hasPermission('store')) {
           return true;
       }
         return false;
     }
 
     /**
      * Determine whether the user can update the model.
      *
      * @param  \App\Models\User  $user
      * @param  \App\Meeting  $meeting
      * @return mixed
      */
     public function update(User $user)
     {
         if ($this->hasPermission('update')) {
             return true;
         }
         return false;
     }
 
     /**
      * Determine whether the user can delete the model.
      *
      * @param  \App\Models\User  $user
      * @param  \App\Meeting  $meeting
      * @return mixed
      */
     public function delete(User $user )
     {
         if ($this->hasPermission('delete')) {
             return true;
         }
         return false;
     }

}
