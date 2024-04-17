<?php

namespace App\Services\Notification\Providers;

use App\User;
use GuzzleHttp\Client;
use App\Services\Notification\Providers\Contracts\Provider;
use Illuminate\Support\Facades\Mail;
use App\Services\Notification\Exceptions\UserHaveNotNumber;
use App\Services\Notification\Exceptions\UserDoesNotHaveNumber;

class SmsProvider implements Provider
{
    private $user;
    private $text;
    public function __construct(User $user, String $text)
    {

        $this->user = $user;
        $this->text = $text;
    }
    public function send()
    {

        $this->havePhoneNumber();

        //to send a request, we use guzzle library here. client comes from GuzzleHttp\Client
        //creating a new object of guzzleHttp\Client
        $client = new Client();


        //to send a request, guzzle has a specific method for each request method.
        //here we want to send a post request for example and we call post method
        //to get the address here, we call the config method to access the files which are in the config folder
        //inside that folder we have services.php which in we already configured the information for sending sms
        $response = $client->post(config('services.sms.uri'), $this->prepareDataForSms());

        //according to the guzzle documentation, if we want to access the response, we need to call the getBody method
        //we return the response so the controller or whoever use our service can work on based on the response
        return $response->getBody();
    }


    private function prepareDataForSms()
    {

        $data = [
            'op' => 'send',
            'message' => $this->text,
            'to' => [$this->user->phone_number],
        ];


        //in guzzle, we need to put our data in a key which called json.
        return [
            'json' => array_merge($data, config('services.sms.auth'))
        ];
    }



    private function havePhoneNumber()
    {
        if (is_null($this->user->phone_number)) {
            throw new UserDoesNotHaveNumber();
        }
    }
}
