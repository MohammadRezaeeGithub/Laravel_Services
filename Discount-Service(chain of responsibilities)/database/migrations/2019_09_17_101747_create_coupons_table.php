<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCouponsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('code')->unique();
            $table->tinyInteger('percent');
            $table->integer('limit');
            $table->date('expire_time');
            //we create the next two columns to say that each coupon can be asigned to the two models
            //here we have a polymorphic relation one to many (see the laravel documentation)
            //for example we want to give a discount to a user
            $table->bigInteger('couponable_id'); //this will be id of the user to whom we want give discount
            $table->string('couponable_type'); //the compelete address of the user model will be here
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
        Schema::dropIfExists('coupons');
    }
}
