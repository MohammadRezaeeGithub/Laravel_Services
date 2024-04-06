<?php

namespace App\Support\Payment\Gateways;

use App\Order;
use Illuminate\Http\Request;

interface GatewayInterface
{
    const TRANSACTION_FAILED = 'transaction.failed';
    const TRANSACTION_SUCCESS = 'transaction.success';


    public function pay(Order $order); //it receives the order and does the payment process
    public function verify(Request $request); //after doing the payment, we verify the payment, if it is successful
    public function getName(): string; //to fill the gateway name in the database
}
