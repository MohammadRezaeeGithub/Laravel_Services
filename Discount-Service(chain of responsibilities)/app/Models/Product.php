<?php

namespace App;

use App\Support\Discount\DiscountCalculator;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    public function hasStock(int $quantity)
    {
        return $this->stock >= $quantity;
    }


    public function decrementStock(int $count)
    {
        return $this->decrement('stock', $count);
    }


    public function category()
    {
        return $this->belongsTo(Category::class);
    }


    //it is a accessor function so to say
    //for the name we should write get / the name of the field we want to get/ attribute
    //it is used to get a property and change it before using it
    //in accessors function laravel pass the value of the field by default
    public function getPriceAttribute($price)
    {
        //chekck if this product has some coupons and thery are valid
        $coupons = $this->category->validCoupons();
        //then check if the collection of the coupons which already recieved is not empty
        if ($coupons->isNotEmpty()) {
            //if the collection is not empty then we new discountCalculator to calculate discount
            $discountCalculator = resolve(DiscountCalculator::class);
            return $discountCalculator->discountedPrice($coupons->first(), $price);
        }

        return $price;
    }
}
