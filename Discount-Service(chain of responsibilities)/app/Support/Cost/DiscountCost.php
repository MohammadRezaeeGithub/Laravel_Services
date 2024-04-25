<?php

namespace App\Support\Cost;

use App\Support\Cost\Contracts\CostInterface;
use App\Support\Discount\DiscountManager;


//we added this class beacust in any case the discount will decorate the main cost and create a new cost
class DiscountCost implements CostInterface
{
    private $cost;

    private $discountManager;


    //we recieve all the dependencies which we need in this class in the constructor
    //the CostInterface that we recieve here is an object of shippingConst (AppServiceProvider)
    public function __construct(CostInterface $cost, DiscountManager $discountManager)
    {
        $this->cost = $cost;
        $this->discountManager = $discountManager;
    }


    public function getCost()
    {
        return $this->discountManager->calculateUserDiscount();
    }

    public function getTotalCosts()
    {
        return $this->cost->getTotalCosts() - $this->getCost();
    }

    public function persianDescription()
    {
        return 'میزان تخفیف';
    }

    public function getSummary()
    {
        return array_merge($this->cost->getSummary(), [$this->persianDescription() => $this->getCost()]);
    }
}
