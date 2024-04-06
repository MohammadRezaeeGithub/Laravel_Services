<?php

namespace App\Support\Basket;

use App\Exceptions\QuantityExceededException;
use App\Product;
use App\Support\Storage\Contracts\StorageInterface;


class Basket
{
    //this basket must have a storage to store its products.
    //in onther words, it will be the StorageInterface instance.
    private $storage;

    public function __construct(StorageInterface $storage)
    {
        $this->storage = $storage;
    }

    //the structure fo the session array will be:
    //[cart,
    //  [productID,
    //      [quantity => quantity]
    //]
    //]



    public function add(Product $product, int $quantity)
    {
        //if the product already exist in the storage
        //we just update the quantity
        if ($this->has($product)) {
            $quantity = $this->get($product)['quantity'] + $quantity;
        }

        //now we pass this new quantity to this function
        //it will check if there are these amount of this product
        //if yes it will update the quantity
        $this->update($product, $quantity);
    }

    public function update(Product $product, int $quantity)
    {
        //checking if we have as much as this quantity in the stock
        if (!$product->hasStock($quantity)) {
            //if there is not, we throw an exception 
            //in the controller we can handle it with try catch
            throw new QuantityExceededException();
        }


        //here we check if the quantity of a product is zero,we remove it from the basket
        if (!$quantity) {
            return $this->storage->unset($product->id);
        }



        //at the end we set the product with its new quantity in the storage
        $this->storage->set($product->id, [
            'quantity' => $quantity
        ]);
    }



    //to recieve the existing product from the basket
    public function get(Product $product)
    {
        return $this->storage->get($product->id);
    }


    //this method will return all the products in the basket
    public function all()
    {
        //in the session we just have the the ID and the quantity of each product
        //but we need all the information related to each product.
        //so first we get the model of each product
        //storage()->all() will return an array which ids are the keys and quantitys are the values.
        //we just need the keys of this array. so we use array_keys() to get the keys.
        $products = Product::find(array_keys($this->storage->all()));

        //after reieving the model of each product, we can now get the quantity of each product
        //so to each model we add a quantity property and get the quantity of each product from session.
        foreach ($products as $product) {
            $product->quantity = $this->get($product)['quantity'];
        }

        return $products;
    }


    //this function will return the total amount of the basket
    public function subTotal()
    {
        $total = 0;
        foreach ($this->all() as $item) {
            $total += $item->price * $item->quantity;
        }


        return $total;
    }


    //this method will return the number of the items in the basket
    //to use this method in the blades file, we need to inject this class in the blade file
    //@inject('basket','app\Support\Basket\Basket')
    //to use in the blade: $basket->itemCount()
    public function itemCount()
    {
        return $this->storage->count();
    }


    //to check if the product is already exist in the basket.
    public function has(Product $product)
    {
        return $this->storage->exists($product->id);
    }


    public function clear()
    {
        return $this->storage->clear();
    }
}
