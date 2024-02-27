<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Laravel\Cashier\Cashier;
use Stripe\Stripe;
use Stripe\PaymentIntent;

class PaymentController extends Controller
{
    /**
     * @admiral9200
     * @abstract This method is used to get all pricing plans to display it in frontend...
     */
    public function getPricingPlans()
    {
        $plans = DB::table("pricing_plans")->get()->all();

        return $plans;
    }

    /**
     * @admiral9200
     * @abstract This method is used to create payment intent...
     */
    public function createPaymentIntent(Request $request)
    {
        $payment = $request->user()->payWith(
            500,
            ['card']
        );

        return $payment->client_secret;
    }

    /**
     * @admiral9200
     * @abstract This method is used to set up a subscription...
     */
    public function setupSubscription(Request $request)
    {
        try {
            Stripe::setApiKey(env('STRIPE_SECRET'));

            // $request->user()
            //     ->newSubscription('default', 'price_1OoAw2CO9QTVzeDyNRC9zwrs')
            //     ->trialDays(3)
            //     ->allowPromotionCodes()
            //     ->create();

            $request->user()
                ->newSubscription('default', 'price_1OoAw2CO9QTVzeDyNRC9zwrs')
                ->createAndSendInvoice();
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    public function createFreeTrials(Request $request)
    {
        try {
            $user = $request->user();
            
            if($user->onTrial('default')) {
                $data['message'] = "User is already in trial!";

                return response()->json($data, 202);
            } else {
                $result = $user->newSubscription('default', 'price_1OoAw2CO9QTVzeDyNRC9zwrs')
                    ->trialDays(3)
                    ->create();

                $data['result'] = $result;

                return response()->json($data, 200);
            }
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    public function checkSubscriptionStatus(Request $request)
    {
        try {
            $user = $request->user();

            // return response()->json($user->onTrial('default'), 200);

            if($user->onTrial('default')) {
                return response()->json(true, 200);
            } else if($user->hasExpiredTrial('default')) {
                return response()->json(false, 201);
            } else if(DB::table("subscriptions")->where('user_id', '=', $user->id)->first()->stripe_status == "active") {
                return response()->json(true, 202);
            } else {
                return response()->json(false, 203);
            }
        } catch(\Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }
}
