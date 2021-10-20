<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Models\Acl;
use App\Models\Module;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // public function role()
    // {
    //     return $this->hasOne('App\Models\Role');
    // }
    public function role()
    {
        return $this->belongsTo('App\Models\Role');
    }
    /**
     * Returns all permissions ACL
     *
     * @return array
     */

    public function check(){
        return false;
    }

    public function verificationModule($module) : bool
    {
       $permissions = $this->hasAnyPermission;
       if (is_array($permissions)) {
           foreach ($permissions as $pr) {
               if ($pr['module_id'] == $module){
                    return true;
               }
           }
       } else {
            if ($permissions['module_id'] == $module) {
                return true;
            }
       }
       
    }

    public function getModule()
    {
        $action = explode('\\',Route::getCurrentRoute()->getAction('controller'));
        $module = Module::where([['name', strtolower($action[3])]])->first();
        if ($module) {
            return $module->id;
        } else {
            return false;
        }
    }

    public function hasAnyPermission(int $roleId, object $action) : bool
    {
       $id = $roleId;
       $acl = false;
       if ($id) {
        $parameterRoute = explode('\\',Route::getCurrentRoute()->getAction('controller'));
        $resultCountArray = count($parameterRoute);
        if ($resultCountArray == 5) {
           $acl = Acl::where([['role_id', $id],['module_id', $this->getModule()]])->get();
        } else {
           $acl = Acl::where([['role_id', $id]])->get();
        }
      }
      if ($acl) {
        foreach ($acl as $permission) {
            if ($this->checkPermission($permission, $action)) {
                return true;
                break;
            }
        }
        return false;
      }
    }

    public function checkPermission(object $permission, object $action) : bool{
        if(is_object($action)){
            if($permission->controller == $action->controller && $permission->action == $action->action){
                return true;
            } else {
                return false;
            }
        }
    }
    /**
     * Check if the user has a role
     *
     * @return boolean
     */
    public function hasRole() 
    { 
        return $this->role ? $this->role->id : false;
    }

    public function adminlte_image()
    {
        $user = User::findOrFail(Auth::user()->id);
        if($user->image != null) {
            return url('/').'/storage/'.$user->image;
        }else{
            return url('/').'/storage/images/avatarDefaultBpd2020.jpg';
        }
    }

    public function adminlte_desc()
    {
        return 'That\'s a nice guy';
    }

    public function adminlte_profile_url()
    {
        return 'profile/username';
    }
}
