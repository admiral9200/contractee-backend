<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Laravel\Cashier\Cashier;
use Stripe\PaymentMethod;

class PaymentController extends Controller
{
    public function getPricingPlans()
    {
        $plans = DB::table("pricing_plans")->get()->all();

        return $plans;
    }
    
    public function subscribe(Request $request)
    {
        $user = $request->user();
        $paymentMethod = PaymentMethod::retrieve($request->input('payment_method_id'));

        $user->createOrGetStripeCustomer();

        $user->newSubscription(
            'default',
            $request->input('plan_id')
        )->create($paymentMethod->id);
    }

    public function createSubscription(Request $request)
    {
        try {
            $request->user()->newSubscription(
                'default',
                'price_monthly'
            )->create($request->paymentMethodId);
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    public function checkout(Request $request)
    {
        $stripePriceId = 'price_deluxe_album';

        $quantity = 1;

        return $request->user()->checkout([$stripePriceId => $quantity], [
            'success_url' => $request->successUrl,
            'cancel_url' => $request->cancelUrl
        ]);
    }

    public function checkoutSuccess(Request $request)
    {
        $sessionId = $request->get('session_id');

        if ($sessionId === null) {
            return;
        }

        $session = Cashier::stripe()->checkout->sessions->retrieve($sessionId);

        if ($session->payment_status !== "paid") {
            return;
        }

        // $orderId = $session['metadata']['order_id'] ?? null;
    }


    public function makeSubscription(Request $request)
    {
        $request->user()->newSubscription(
            'default',
            20
        )->create($request->paymentMethodId);
    }

    public function createPaymentIntent(Request $request)
    {
        $amount = 
        $payment = $request->user()->payWith(
            500, ['card']
        );

        return $payment->client_secret;
    }
}
