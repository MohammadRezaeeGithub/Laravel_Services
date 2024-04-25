<?php

namespace App\Support\Discount;

use App\Coupon;


//this class has only one task:calculating the amount of discount 
class DiscountCalculator
{
    //calculates the amount of discount and checks it is exceeds the limit for the discount
    //this one calculates the amount of discount
    public function discountAmount(Coupon $coupon, int $amount)
    {
        $discountAmount = (int) (($coupon->percent / 100) * $amount);

        return  $this->isExceeded($discountAmount, $coupon->limit) ? $coupon->limit : $discountAmount;
    }


    //return the final amount after the discount
    public function discountedPrice(Coupon $coupon, int $amount)
    {
        return $amount - $this->discountAmount($coupon, $amount);
    }


    //check if the discount exceeds the limit
    private function isExceeded(int $amount, int $limit)
    {
        return  $amount > $limit;
    }
}
