<?php

namespace App\Support\Discount\Coupon\Validator;

use App\Coupon;
use App\Exceptions\IllegalCouponException;
use App\Support\Discount\Coupon\Validator\Contracts\AbstractCouponValidator;

//this class checks if the user can use this coupon or discount
class CanUseIt extends AbstractCouponValidator
{
    public function validate(Coupon $coupon)
    {
        //for this part which we need to get all the coupons for the user
        //we must defined the Couponable trait and make sure of morph relation
        if (!auth()->user()->coupons->contains($coupon)) {
            throw new IllegalCouponException();
        }

        return parent::validate($coupon);
    }
}
