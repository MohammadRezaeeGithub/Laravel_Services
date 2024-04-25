<?php

namespace App\Support\Discount\Coupon\Validator\Contracts;

use App\Coupon;


//this class forces some contracts to the classes which is responsible for discount validation
interface CouponValidatorInterface
{
    //to set the next validator, for example when we want to know after the date we have to validate what.
    public function setNextValidator(CouponValidatorInterface $validator);
    //the logic that should be checked will be in this method
    public function validate(Coupon $coupon);
}
