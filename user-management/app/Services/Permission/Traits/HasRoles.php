<?php

namespace App\Services\Permission\Traits;

use App\Role;


//this trait is used in the user model to asign roles to the users
trait HasRoles
{
    //definition the relation between user model and role model
    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }


    public function giveRolesTo(...$roles)
    {
        $roles = $this->getAllRoles($roles);

        if ($roles->isEmpty()) return $this;

        $this->roles()->syncWithoutDetaching($roles);

        return $this;
    }


    public function withdrawRoles(...$roles)
    {
        $roles = $this->getAllRoles($roles);

        $this->roles()->detach($roles);

        return $this;
    }


    public function refreshRoles(...$roles)
    {
        $roles = $this->getAllRoles($roles);
        $this->roles()->sync($roles);
        return $this;
    }


    public function hasRole(string $role)
    {
        return $this->roles->contains('name', $role);
    }


    protected function getAllRoles(array $roles)
    {
        return Role::whereIn('name', array_flatten($roles))->get();
    }
}
