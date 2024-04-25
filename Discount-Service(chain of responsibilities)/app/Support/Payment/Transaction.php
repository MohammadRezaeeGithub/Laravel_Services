<?php

namespace App\Support\Payment;

use App\Events\OrderRegistered;
use App\Invoice;
use App\Order;
use App\Payment;
use App\Support\Cost\Contracts\CostInterface;
use Illuminate\Http\Request;
use App\Support\Basket\Basket;
use App\Support\Payment\Gateways\GatewayInterface;
use App\Support\Payment\Gateways\Pasargad;
use App\Support\Payment\Gateways\Saman;
use Illuminate\Support\Facades\DB;

class Transaction
{
    private $request;

    private $basket;

    /**
     * @var CostInterface
     */
    private $cost;


    //now that we change the way we calculate our cost, we need to change our transaction
    //here we get an object of CostInterface
    public function __construct(Request $request, Basket $basket, CostInterface $cost)
    {
        $this->request = $request;
        $this->basket = $basket;
        $this->cost = $cost;
    }


    public function checkout()
    {
        DB::beginTransaction();

        try {

            $order = $this->makeOrder();

            $order->generateInvoice();


            //we need to do some changes in makePaymnet function
            $payment = $this->makePayment($order);



            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return null;
        }

        if ($payment->isOnline()) {
            //here that we want to pay, we pass the order and the total cost from InterfaceCost
            //beacuse of this change, we need to change the gateway interface as well
            return $this->gatewayFactory()->pay($order, $this->cost->getTotalCosts());
        }

        $this->completeOrder($order);

        return $order;
    }

    public function pay(Order $order)
    {

        return $this->gatewayFactory()->pay($order, $order->payment->amount);
    }



    public function verify()
    {
        $result = $this->gatewayFactory()->verify($this->request);

        if ($result['status'] === GatewayInterface::TRANSACTION_FAILED) return false;

        $this->confirmPayment($result);

        $this->completeOrder($result['order']);

        return true;
    }



    private function completeOrder($order)
    {

        $this->normalizeQuantity($order);

        event(new OrderRegistered($order));

        $this->basket->clear();

        //here we need to clear the session from coupons
        session()->forget('coupon');
    }





    private function normalizeQuantity($order)
    {
        foreach ($order->products as $product) {
            $product->decrementStock($product->pivot->quantity);
        }
    }




    private function confirmPayment($result)
    {
        return $result['order']->payment->confirm($result['refNum'], $result['gateway']);
    }




    private function gatewayFactory()
    {

        if (!$this->request->has('gateway')) return resolve(Saman::class);

        $gateway = [
            'saman' => Saman::class,
            'pasargad' => Pasargad::class
        ][$this->request->gateway];
        return resolve($gateway);
    }

    private function makeOrder()
    {
        $order = Order::create([
            'user_id' => auth()->user()->id,
            'code' => bin2hex(str_random(16)),
            'amount' => $this->basket->subTotal()
        ]);

        $order->products()->attach($this->products());

        return $order;
    }


    private function makePayment($order)
    {
        return Payment::create([
            'order_id' => $order->id,
            'method' => $this->request->method,
            // 'amount' => $order->amount,
            //now instead of getting the amount from the order, we get it from CostInterface->getTotalCosts()
            'amount' => $this->cost->getTotalCosts()
        ]);
    }


    private function products()
    {
        foreach ($this->basket->all() as $product) {
            $products[$product->id] = ['quantity' => $product->quantity];
        }

        return $products;
    }
}
