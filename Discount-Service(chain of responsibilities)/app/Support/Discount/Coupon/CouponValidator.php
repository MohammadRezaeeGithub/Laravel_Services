<?php

namespace App\Support\Discount\Coupon;

use App\Coupon;
use App\Support\Discount\Coupon\Validator\CanUseIt;
use App\Support\Discount\Coupon\Validator\IsExpired;


//this class is defined to use all the validator methods
//and we call this class in the controllers
class CouponValidator
{
    public function isValid(Coupon $coupon)
    {
        //here we need to new all the validators and execute the valide methods
        $isExpired = resolve(IsExpired::class);
        $canUseIt = resolve(CanUseIt::class);
        //$anotherValidator = new AnotherValidator


        //here we set the next validator
        //we call teh setNextValidator(defined in the abstract class) of isExpired which (extened abstract class)
        $isExpired->setNextValidator($canUseIt);
        //$canUseIt->setNextvalidator($anotherValidator)


        return $isExpired->validate($coupon);
    }
}
