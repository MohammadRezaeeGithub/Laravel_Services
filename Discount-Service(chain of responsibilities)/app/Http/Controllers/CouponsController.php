<?php

namespace App\Http\Controllers;

use App\Coupon;
use App\Support\Discount\Coupon\CouponValidator;
use Illuminate\Http\Request;

class CouponsController extends Controller
{
    /**
     * @var CouponValidator
     */
    private $validator;


    public function __construct(CouponValidator $validator)
    {
        //we check that if someone wants to use this controller must be logged in
        $this->middleware('auth');
        $this->validator = $validator;
    }



    //this method is called when the user want to use his coupon in the payment page
    public function store(Request $request)
    {

        //to validate the coupon
        //to check if the user is allowed to use it
        //put the coupon into the user's session
        //redirect the user
        try {
            $request->validate([
                //the second parameter checks if the coupon exists in the coupons table in the code column
                'coupon' => ['required', 'exists:coupons,code']
            ]);

            //getting the coupon from the databse
            $coupon = Coupon::where('code', $request->coupon)->firstOrFail();

            //now we need to check if the user can use this coupon (chain of responsibility design pattern)
            //validator is an object of CouponValidator class 
            //in which we new all the validators and execute their validation methods
            $this->validator->isValid($coupon);


            //putting the user's coupon into the session
            session()->put(['coupon' => $coupon]);


            return redirect()->back()->withSuccess('کد تخفیف با موفقیت اعمال شد');
        } catch (\Exception $e) {
            return redirect()->back()->withError('کد تخفیف نامعتبر میباشد');
        }
    }


    //this function is used to remove a coupon from the user's session
    public function remove()
    {
        session()->forget('coupon');


        return back();
    }
}
