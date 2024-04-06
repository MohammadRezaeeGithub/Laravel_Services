<?php

namespace App\Support\Storage\Contracts;


//it is like a contract. all kinds of storages in this system must implement this interface.
interface StorageInterface
{
    public function get($index); //to recieve a value from our storage or basket
    public function set($index, $value); //to add a new value into our storage or basket
    public function all(); //return all the values which exist in our storage or basket.
    public function exists($index); //to check if a value exists in our storage or basket by its index.
    public function unset($index); //to remove a value from our storage or basket by its index.
    public function clear(); //it will clean up our storage or basket
}
