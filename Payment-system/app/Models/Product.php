<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    //we pass a number of products and this method checks if there are as many as we pass of this product.
    public function hasStock(int $quantity)
    {
        return $this->stock >= $quantity;
    }


    public function decrementStock(int $count)
    {
        //elequent has a decrement method, it will decrease the amount of the field which we pass to this function
        return $this->decrement('stock', $count);
    }
}
