<?php

namespace App\Services\Permission\Traits;

use App\Permission;

//this trait is used in the models which needs to use the permisiions
trait HasPermissions
{

    //this function defines the relationship between the user and the permission
    //this relationship is many to many
    public function permissions()
    {
        //the second parameter is the interface table name 
        //which is optional if we named it based on the involved models
        return $this->belongsToMany(Permission::class);
    }


    //this funciton is used to asign a permission to a user
    //it will recieve a list of permissions
    public function givePermissionsTo(...$permissions)
    {

        //we recieve a string as a permission, so we need first to recieve the model of the this permission
        $permissions = $this->getAllPermissions($permissions);

        //we check if the permissions is empty, we return the model itself
        if ($permissions->isEmpty()) return $this;


        //Laravel gives us some methods to asign and delete in this kind of relationship
        //attach \: this method is used to asign permissions to a user
        //detach \: this method is used to remove permissions to a user
        //sync \: this method is used to remove all previous permissions and set only the new permissions which we gave him
        $this->permissions()->syncWithoutDetaching($permissions);


        return $this;
    }


    //this function is used to remove a permission or permissions from a user
    public function withdrawPermissions(...$permissions)
    {
        //like the pervious function, first we find the permissions's model
        //but this time we do not care if the permissions is empty
        $permissions = $this->getAllPermissions($permissions);

        //we just detach the permissions from the user
        $this->permissions()->detach($permissions);

        return $this;
    }


    //when we want to remove all user's permissions and set new permissions to this user
    public function refreshPermissions(...$permissions)
    {
        $permissions = $this->getAllPermissions($permissions);

        $this->permissions()->sync($permissions);


        return $this;
    }


    //to check if the user has a permission to do a task
    //we pass the permission as the argument
    public function hasPermission(Permission $permission)
    {

        //from this user's model, first we recieve all the his permissions and check if the passed permission exists among them (contains fuction)
        return $this->hasPermissionsThroughRole($permission) || $this->permissions->contains($permission);
    }


    protected function hasPermissionsThroughRole(Permission $permission)
    {
        //in this function first we check to to which roles this permisstion belongs
        //then we recieve all the roles of the user 
        //at the end if there a role or roles in common, it means the user has the permission
        foreach ($permission->roles as $role) {
            if ($this->roles->contains($role)) return true;
        }
        return false;
    }


    //this function is used to get all the MODELS of  permissions from the list of permissions
    protected function getAllPermissions(array $permissions)
    {
        //we used the whereIn function beacause we pass an array of strings and we want to search in the name coulumn
        //if user pass the permissions in an array, we use array_flatten function to return it to normal string
        return Permission::whereIn('name', array_flatten($permissions))->get();
    }
}
