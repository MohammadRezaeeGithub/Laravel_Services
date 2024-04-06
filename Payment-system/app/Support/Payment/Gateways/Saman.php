<?php

namespace App\Support\Payment\Gateways;

use App\Order;
use Illuminate\Http\Request;

class Saman implements GatewayInterface
{
    private $merchantID;
    private $callback;

    public function __construct()
    {
        $this->merchantID = '452585658';
        $this->callback = route('payment.verify', $this->getName());
    }




    public function pay(Order $order)
    {
        $this->redirectToBank($order);
    }


    //sending the request to the bank with the requested information
    //after the payment at the bank, the user will be redirected to the callback url
    //in that url, which in this case is PaymentController verify method, 
    //we will verify the payment with bank's infromation and documents
    private function redirectToBank($order)
    {
        $amount = $order->amount + 10000;
        echo "<form id='samanpeyment' action='https://sep.shaparak.ir/payment.aspx' method='post'>
		<input type='hidden' name='Amount' value='{$amount}' />
		<input type='hidden' name='ResNum' value='{$order->code}'>
		<input type='hidden' name='RedirectURL' value='{$this->callback}'/>
		<input type='hidden' name='MID' value='{$this->merchantID}'/>
		</form><script>document.forms['samanpeyment'].submit()</script>";
    }


    public function verify(Request $request)
    {
        // if (!$request->has('State') || $request->input('State') !== "OK") {
        //     return $this->transactionFailed();
        // }

        //soap is sending information protocol which is used to communicate with the bank 
        $soapClient = new \SoapClient('https://acquirer.samanepay.com/payments/referencepayment.asmx?WSDL');

        //we call the VerifyTransaction method of the soap client and pass the information which is needed and asked by the bank
        $response = $soapClient->VerifyTransaction($request->input('RefNum'), $this->merchantID);

        //based on the back document we need to check the amount of order and the amount of payment to verify the transaction
        $order = $this->getOrder($request->input('ResNum'));

        //here we just simulate the positive response from the bank
        //just to verify our programme
        $response = $order->amount + 10000;
        $request->merge(['RefNum' => '45852525']);

        //to verify if the response from the bank is the same of the order amount plus the cost  of shipping
        return $response == ($order->amount + 10000)
            ? $this->transactionSuccess($order, $request->input('RefNum'))
            : $this->transactionFailed();
    }


    //this method will be called when we want to return the result of the transaction verification
    //this one will return the successful result
    private function transactionSuccess($order, $refNum)
    {
        return [
            'status' => self::TRANSACTION_SUCCESS,
            'order' => $order,
            'refNum' => $refNum,
            'gateway' => $this->getName()
        ];
    }


    //this method to get the order by resNum
    private function getOrder($resNum)
    {
        return Order::where('code', $resNum)->firstOrFail();
    }

    //this method will be called when we want to return the result of the transaction verification
    //this one will return the failed result
    private function transactionFailed()
    {
        return [
            'status' => self::TRANSACTION_FAILED
        ];
    }


    public function getName(): string
    {
        return 'saman';
    }
}
