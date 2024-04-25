<?php

namespace App\Support\Payment\Gateways;

use App\Order;
use Illuminate\Http\Request;

interface GatewayInterface
{
    const TRANSACTION_FAILED = 'transaction.failed';
    const TRANSACTION_SUCCESS = 'transaction.success';


    //here we add an amount to the pay method
    //we need to go to the Saman gateway for example and change the pay method
    public function pay(Order $order, int $amount);
    public function verify(Request $request);
    public function getName(): string;
}
