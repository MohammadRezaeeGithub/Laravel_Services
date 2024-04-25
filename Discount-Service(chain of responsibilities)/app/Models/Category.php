<?php

namespace App;

use App\Support\Discount\Coupon\Traits\Couponable;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    //use the couponable train which is created for the models which want to use the coupon
    use Couponable;
}
