<?php

namespace App\Support\Cost;

use App\Support\Basket\Basket;
use App\Support\Cost\Contracts\CostInterface;


//this class is supposed to return the total cost of the basket and only the costs related to the basket
//then we bind this class in AppServiceProvider in app/Providers/AppServiceProvider
//it means whenever someone calls CostInterface, Laravel will return the BasketCost
class BasketCost implements CostInterface
{
    /**
     * @var Basket
     */
    private $basket;

    public function __construct(Basket $basket)
    {
        $this->basket = $basket;
    }



    //it returns the total cost of the basket
    public function getCost()
    {
        return $this->basket->subTotal();
    }


    //this method is supposed to return the previous cost that we have to pay.
    //for example, in the total amount,we have the shipping cost and basket cost. so basket cost is the previous const for shipping
    //but here the basket cost is the basket cost and we return only basket cost
    public function getTotalCosts()
    {
        return $this->getCost();
    }

    public function persianDescription()
    {
        return 'سبد خرید';
    }

    //this method will return an array which the key is the persian description of the cost
    //and the value is the amount of the cost
    public function getSummary()
    {
        return [$this->persianDescription() => $this->getCost()];
    }
}
