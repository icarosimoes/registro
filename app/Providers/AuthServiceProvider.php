<?php

namespace App\Providers;

use App\CheckSuite;
use App\Models\Meeting\meeting;
use App\Policies\CheckSuites\CheckSuitesPolicy;
use App\Policies\Meeting\MeetingPolicy;
use GuzzleHttp\Psr7\Request;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        meeting::class => MeetingPolicy::class,
        CheckSuite::class => CheckSuitesPolicy::class
        
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        // Gate::define('checkPermission', function($user){
        //     // ckeck role
        //     $role = $user->hasRole();
        //     if(!$role){
        //         return false;
        //     }else{
        //             $action = $this->getAction();
        //             $ignoreRoute = config('accesscontrollist')['ignore.routes'];
        //             $getName = Route::getCurrentRoute()->getName(); 
        //             if(!in_array($getName, $ignoreRoute)){
        //                 return $user->hasAnyPermission($role, $action);
        //             }
        //             return true;
        //     }
        // });

        // Gate::define('checkRouters', function($user, $route){
        //     $role = $user->hasRole();
        //     if(!$role){
        //         return false;
        //     }else{
        //             $action = $this->getActionString($route);
        //         return $user->hasAnyPermission($role, $action);
        //     }
        // });
    }
    protected function getAction() 
    {
        // $action = explode('\\',Route::getCurrentRoute()->getAction('controller'));
        // $resultCountArray = count($action);
        // if ($resultCountArray == 5) {
        //     $controllerAction = explode('@',$action[4]);
        // }else {
        //     $controllerAction = explode('@',$action[3]);
        // }
        // return (object) [
        //     'controller' => $controllerAction[0],
        //     'action'    => $controllerAction[1]
        // ];
    }
    protected function getActionString($route) 
    {
        // $action = explode('\\',Route::getRoutes()->getByName($route)->action['controller']); 
        // $resultCountArray = count($action);
        // if ($resultCountArray == 5) {
        //     $controllerAction = explode('@',$action[4]);
        // }else {
        //     $controllerAction = explode('@',$action[3]);
        // }
        // return (object) [
        //     'controller' => $controllerAction[0],
        //     'action'    => $controllerAction[1]
        // ];
    }
}
