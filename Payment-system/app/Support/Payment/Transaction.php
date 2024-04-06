<?php

namespace App\Support\Payment;

use App\Events\OrderRegistered;
use App\Order;
use App\Payment;
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


    public function __construct(Request $request, Basket $basket)
    {
        $this->request = $request;
        $this->basket = $basket;
    }


    public function checkout()
    {
        //sometimes some problems might happen during the checkout process
        //and we want to rollback all the queries that were made in database
        //to rollback these queries, we can do like this:
        //it means that from now on, all the queries that i made might be rolled back
        DB::beginTransaction();

        //then we put the part of our program that might make some errors
        try {

            //as a frist step we create a new order for user
            $order = $this->makeOrder();

            //in the second step we create a new payment for the order
            $payment = $this->makePayment($order);

            //after making the payment, if the payment method is cart or cash, we do not need to do anything else



            //if these parts do not make any errors, we call DB::commit()
            //it makes permanent all the changes in the database
            DB::commit();
        } catch (\Exception $e) {
            //but if those parts make some errors, we call DB::rollBack()
            //to rollback all the changes in the database
            DB::rollBack();
            return null;
        }

        //check if the payment method was online
        if ($payment->isOnline()) {
            //form the choosen gateway, we call the pay method and pass the order
            return $this->gatewayFactory()->pay($order);
        }

        $this->completeOrder($order);

        return $order;
    }


    //this method will be called in PaymentController verify method
    //it will verfiy if the payment was successful
    public function verify()
    {
        //it will call the verify method of the choosen gateway (saman or pasargad)
        //here the result will be the order,refNum and gateway name
        $result = $this->gatewayFactory()->verify($this->request);

        //here we check if the result status is not successfull, we return false
        if ($result['status'] === GatewayInterface::TRANSACTION_FAILED) return false;

        //we pass the result which contains the order,refNum and gateway name to do the process of changing the database
        $this->confirmPayment($result);

        //then we pass order to this function to decrease the number of the products in the order from the stock of each product
        //and send and email to the user
        //and then clear the basket
        $this->completeOrder($result['order']);


        return true;
    }


    private function completeOrder($order)
    {

        $this->normalizeQuantity($order);

        //to send an emial to user with his order details, we need to do like this:
        //1- create and event, 2- create a listener for that event 3-create a mailable
        //then in EventServiceProvider we need to connect the event and listener
        //in the event file we need create a order property in constructure
        //
        event(new OrderRegistered($order));

        $this->basket->clear();
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




    //this function verify the user choose which gateway to use
    private function gatewayFactory()
    {
        //check which gateway is selected
        $gateway = [
            'saman' => Saman::class,
            'pasargad' => Pasargad::class
        ][$this->request->gateway];

        //then we return an object of choosen gateway
        return resolve($gateway);
    }


    //this function is used in checkout method to create a new order
    private function makeOrder()
    {
        $order = Order::create([
            'user_id' => auth()->user()->id,
            'code' => bin2hex(str_random(16)), //making the unique code for each order
            'amount' => $this->basket->subTotal()
        ]);

        //since each order has some products, 
        //we register the products related to this order in the database, product_order table
        //to pass the external information to attache method, we need to respect a specific form
        //for that we create products method
        //CHEKC THE LARAVEL DOCUMENTATION, MANY TO MANY RELATIONS, ATTACH METHOD
        $order->products()->attach($this->products());

        return $order;
    }


    //making the payment for the order
    private function makePayment($order)
    {
        return Payment::create([
            'order_id' => $order->id,
            'method' => $this->request->method,
            'amount' => $order->amount
        ]);
    }



    //using this method, we created specific form for the data which we should pass to the attache method
    private function products()
    {
        foreach ($this->basket->all() as $product) {
            //here we set the id of the product as the key
            //and then as a value the amoutn of that product 
            $products[$product->id] = ['quantity' => $product->quantity];
        }

        return $products;
    }
}
