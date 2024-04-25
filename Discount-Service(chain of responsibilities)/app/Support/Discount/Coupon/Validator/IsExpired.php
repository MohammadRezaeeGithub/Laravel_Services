<?php

namespace App\Support\Discount\Coupon\Validator;

use App\Coupon;
use App\Exceptions\CouponHasExpiredException;
use App\Support\Discount\Coupon\Validator\Contracts\AbstractCouponValidator;


//now this class extends AbstractCouponValidator instead of the CouponValidatorInterface
//beacuse of the logic we implement in Contract folder
class IsExpired extends AbstractCouponValidator
{
    public function validate(Coupon $coupon)
    {
        //need to check if the coupon is expired
        //for that in the Coupon medel, we define a function to check if the coupon is expired
        if ($coupon->isExpired()) {
            throw new CouponHasExpiredException();
        }

        //when the coupon is not expired, we have to call the next validator
        //so from the parent class we call teh validate method and pass the coupon
        return parent::validate($coupon);
    }
}
