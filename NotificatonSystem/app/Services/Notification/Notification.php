<?php

namespace App\Services\Notification;

use App\Services\Notification\Providers\Contracts\Provider;

/**
 * @method sendSms(\App\User $user , String $text)
 * @method sendEmail(\App\User $user , \Illuminate\Mail\Mailable $mailable)
 */

class Notification
{

    public function __call($method, $arguments)
    {
        //namspace gives us the namespace of this file which is app/services/notifications
        //then we add providers and the name of the service provider
        $providerPath = __NAMESPACE__ . '\Providers\\' .  substr($method, 4) . 'Provider';

        //check if the provider class exists
        if (!class_exists($providerPath)) {
            throw new \Exception("Class does not exist");
        }
        $providerInstance = new $providerPath(...$arguments);

        //check if  the provider has a send method
        //so for that we define an interface and each provider should implement it
        if (!is_subclass_of($providerInstance, Provider::class)) {
            throw new \Exception("class must implements \App\Services\Notification\Providers\Contracts\Provider");
        }
        return $providerInstance->send();
    }
}
