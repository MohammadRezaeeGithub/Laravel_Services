<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = ['user_id', 'code', 'amount'];



    public function products()
    {
        //our order_product table has another column and we could add it here
        //each we reach an object of the order, it will return the quantity as well
        return $this->belongsToMany(Product::class)->withPivot('quantity');
    }


    public function payment()
    {
        return $this->hasOne(Payment::class);
    }



    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
