<?php

namespace App\Http\Middleware;

use Closure;

//we create this middleware to check a permission even before going to a route by user
//we have to register this middleware in the app/http/kernel.php file
//we can use also the Laravel's gate as a middleware => search in the laravel documentation
class RoleMiddleware
{
    //this middleware will recieve an name which in this case is $role, 
    //the name which we pass to middlewae
    public function handle($request, Closure $next, $role)
    {
        //then we check if there is a user in the request
        //or if there is a user,we check if the user has the role
        if (!$request->user() || !$request->user()->hasRole($role)) {
            //if one the coditions above were not true, we redirect the user to another route
            abort(404);
        }
        return $next($request);
    }
}
