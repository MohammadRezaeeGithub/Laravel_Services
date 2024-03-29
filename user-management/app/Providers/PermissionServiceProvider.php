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


        Blade::if('role', function ($role) {
            return auth()->check() && auth()->user()->hasRole($role);
        });
    }
}
