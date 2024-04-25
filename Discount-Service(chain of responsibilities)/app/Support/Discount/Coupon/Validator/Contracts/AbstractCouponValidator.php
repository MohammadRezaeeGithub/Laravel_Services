<?php

namespace App\Support\Discount\Coupon\Validator\Contracts;

use App\Coupon;
//we could define this class and put the common logic of the concrete classes
//when we define those classes, we don't need to write repetitive code
abstract class AbstractCouponValidator implements CouponValidatorInterface
{
    //defining this variable to declare next validator which must be called
    private $nextValidator;
    public function setNextValidator(CouponValidatorInterface $validator)
    {
        $this->nextValidator = $validator;
    }


    public function validate(Coupon $coupon)
    {
        //whenever the nextvalidator is null, it means the chain is ended and we don't have anything else to validate.
        if ($this->nextValidator === null) {
            return true;
        }

        //nextvalidator is the instance which we passed to the constructor
        //and the validate method comes from that instance,(interface class definition)
        return $this->nextValidator->validate($coupon);
    }
}
