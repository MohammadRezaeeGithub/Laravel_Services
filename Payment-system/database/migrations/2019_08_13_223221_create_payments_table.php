<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->bigIncrements('id');
            //eache payment has done for a order. so we need to know which order
            $table->bigInteger('order_id');
            //online or cash or ....
            $table->string('method');
            $table->string('gateway')->nullable();
            //when the payment is done, the bank will give us a refrence number for the payment
            $table->string('ref_num')->nullable();
            //the total migh be different from the order amount because of tax ....
            $table->integer('amount');
            $table->tinyInteger('status')->comment('0 : Incomlete , 1 : Complete');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payments');
    }
}
