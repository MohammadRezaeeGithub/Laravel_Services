<?php

namespace App\Support\Storage;

use App\Support\Storage\Contracts\StorageInterface;
use Countable;


//this class is created to manage our storage in Session.
//it must implement the StorageInterface interface and another php interface which is called Countable.
//when a calss implements the Countable interface, it will be countable.
//NOW WE TELL LARAVEL WHEN WE SAY STORAGEINTERFACE,IT HAS TO CREATE AN INSTANCE OF THIS CLASS.
//MYBE LATER WE WANT TO SOTRE THE BASKET ON THE DATABASE, EVEN THEN WE CALL STORAGEINTERFACE, BUT
//WE TELL LARAVEL TO RETURN THE CLASS CORESSPONDS TO THAT MOMENT.
//WE HAVE TO NOTIFY LARAVEL IN AppSeviceProvider.php ABOUT IT (boot method).
//then when want to use it we do like this: resolve(StorageInterface::class)
class SessionStorage implements StorageInterface, Countable
{
    //since sessions is a key value array, we use this variable to store the name of key 
    private $bucket;

    //when we create a new instance of this class, we will pass the name of the bucket (key)
    public function __construct($bucket = 'default')
    {
        $this->bucket = $bucket;
    }

    //WITH THIS STRUCTURE THE FINAL SHPE OF THE SEESSION ARRAY WILL BE:
    //['cart',[index=>value]]  => the final shape of the session array.

    public function get($index)
    {
        return session()->get($this->bucket . '.' . $index);
    }

    public function set($index, $value)
    {
        //the result in the session will be like this:
        //['cart',[index=>value]]  => the final shape of the session array.
        return session()->put($this->bucket . '.' . $index, $value);
    }

    public function all()
    {
        return session()->get($this->bucket) ?? [];
    }

    public function exists($index)
    {
        return session()->has($this->bucket . '.' . $index);
    }

    public function unset($index)
    {
        return session()->forget($this->bucket . '.' . $index);
    }

    public function clear()
    {
        return session()->forget($this->bucket);
    }

    public function count()
    {
        return count($this->all());
    }
}
