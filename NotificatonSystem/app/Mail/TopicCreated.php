<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class TopicCreated extends Mailable
{
    use Queueable, SerializesModels;
    //if we want these properties to be available in the blades file,they should be public
    //but we can define them private and yet send them to the view(in build method)
    private $first_name;
    private $last_name;

    public function __construct()
    {
        $this->first_name  = 'mehrdad';
        $this->last_name = 'saami';
    }


    public function build()
    {
        //here we can pass our private properties to the view.
        //if they were public, they would be available in the view themselves and we did not need to send them there
        return $this->view('emails.topic-created')->with([
            'full_name' => $this->first_name . $this->last_name
        ]);
    }
}
