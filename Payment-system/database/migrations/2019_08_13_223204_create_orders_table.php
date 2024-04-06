<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            //beacause each order is done by a user, we need to know who has done this order
            $table->bigInteger('user_id');
            //it is unique code and we use it when we want to send it to the payment gateway 
            $table->string('code', 250)->unique();
            //the total amount of each order
            $table->integer('amount');
            $table->timestamps();
        });
        //the begining of the Id column will be 100000
        //and we can pass this code to user if he wants to follow up later his order
        \DB::update('alter table orders AUTO_INCREMENT = 100000');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
