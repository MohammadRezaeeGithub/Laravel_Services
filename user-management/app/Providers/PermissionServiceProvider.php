<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Permission;
use Illuminate\Support\Facades\Blade;


//after creating this service provider we have to register it in laravel service provider
//config/app.php -> providers
class PermissionServiceProvider extends ServiceProvider
{




    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //with this difinitiion, we can use CAN directive in blade to check the user's permissions
        //and in the backend (controllers) we can use AUTHRIZE and CAN method to check the user's permission
        //if the user has the permission to do the task
        Permission::all()->map(function ($permission) {
            //gate is onther name for permission in Laravel
            Gate::define($permission->name, function ($user) use ($permission) {
                return $user->hasPermission($permission);
            });
        });


        //to check if the user has a role to show some buttons or something like that 
        //we create a blade directive to check if the user has this role
        //in the callback function we pass the name which we pass to the directive in blade file
        Blade::if('role', function ($role) {
            //here we check if user is logged in and this user has this role
            return auth()->check() && auth()->user()->hasRole($role);
        });
    }
}
