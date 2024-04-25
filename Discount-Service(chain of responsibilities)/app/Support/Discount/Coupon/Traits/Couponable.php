<?php

namespace App\Support\Discount\Coupon\Traits;

use App\Coupon;
use Carbon\Carbon;


//this trait is supposed to apply different coupon on different models
trait Couponable
{
    //this method is supposed to return all the coupons
    public function coupons()
    {
        //return the morph relation 
        //this trait is supposed to be used in user model and act like a relation
        //so we have to return the coupon and second parameter comes from the name of coulmn in database
        //it was couponable_id and couponable_type, so we wrote couponable
        //then we need to tell the user medel to use this trait 
        return $this->morphMany(Coupon::class, 'couponable');
    }


    //
    public function validCoupons()
    {
        //we return just the coupons which are still valid, it means that the expire time is not expired
        //it returns all the coupons which still valids 
        return $this->coupons->where('expire_time', '>', Carbon::now());
    }
}
