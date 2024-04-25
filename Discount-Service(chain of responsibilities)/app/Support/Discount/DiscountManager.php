<?php

namespace App\Support\Discount;

use App\Support\Cost\BasketCost;


//here we are supposed to get the discount code from the session and calculate the amount user should pay
class DiscountManager
{
    private $basketCost;

    private $discountCalculator;


    //we get the basket cost, beacuse we want to apply the discount  on the basket cost
    public function __construct(BasketCost $basketCost, DiscountCalculator $discountCalculator)
    {
        $this->basketCost = $basketCost;
        $this->discountCalculator = $discountCalculator;
    }

    public function calculateUserDiscount()
    {

        //if there is no a key which is called coupon, it means there is no discount
        if (!session()->has('coupon')) return 0;

        //but here it means there is a key with the name of the coupon, and we need to calculate the discount
        //for that we create another class discountCalculator
        return $this->discountCalculator->discountAmount(session()->get('coupon'), $this->basketCost->getTotalCosts());
    }
}
