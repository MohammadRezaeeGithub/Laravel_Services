<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'order_id', 'amount', 'status', 'method', 'gateway', 'ref_num'
    ];


    //when we create a new payment, we might not fill all the fields
    //here we could mention some default values for the fields
    //if we didn't fill them, they will be filled with default values
    protected $attributes = [
        'status' => 0
    ];


    //just to verify that the payment is online or not
    public function isOnline()
    {
        return $this->method === 'online';
    }



    //this method will be called at the end of the transaction verification
    //it will fill the fields of the payment in the database
    //including the status to payed
    //gateway
    //and the reference number of the transaction which we recieved from the bank
    public function confirm(string $refNum, string $gateway = null)
    {
        $this->ref_num = $refNum;
        $this->gateway = $gateway;
        $this->status = 1;
        $this->save();
    }
}
