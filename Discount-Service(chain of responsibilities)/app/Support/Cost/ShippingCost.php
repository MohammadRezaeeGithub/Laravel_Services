<?php

namespace App\Support\Cost;

use App\Support\Cost\Contracts\CostInterface;


//defining this class to return the cost of the shipping
class ShippingCost implements CostInterface
{
    /**
     * @var CostInterface
     */
    private $cost;
    const SHIPPING_COST = 20000;

    //first we pass the previous cost which is the cost of the basket
    //the object that we recieve here comes from the AppServiceProvider where we bind
    public function __construct(CostInterface $cost)
    {
        $this->cost = $cost;
    }


    //the cost of shipping is the amount that we deifned already and it is constant 
    public function getCost()
    {
        return self::SHIPPING_COST;
    }


    //to have the total cost, we get the total cost(basket cost) and add it to the shipping cost
    public function getTotalCosts()
    {
        return $this->cost->getTotalCosts() + $this->getCost();
    }

    public function persianDescription()
    {
        return 'هزینه حمل و نقل';
    }

    //here we merge two arrays, the previous summary(basket summary ) and the the summary of this class(cost)
    public function getSummary()
    {
        return array_merge($this->cost->getSummary(), [$this->persianDescription() => $this->getCost()]);
    }
}
