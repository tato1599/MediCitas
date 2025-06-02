<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session;

class StripeCheckoutController extends Controller
{
    public function checkout(Request $request)
    {
        \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'mxn',
                    'product_data' => [
                        'name' => 'suscripción mensual',
                    ],
                    'unit_amount' => 29900,
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => route('checkout.success'),
            'cancel_url' => route('checkout.cancel'),
        ]);

        return redirect($session->url);
    }

    public function success(Request $request)
{
    // Guarda en sesión que ya pagó
    $request->session()->put('paid', true);

    // Redirige al formulario de registro
    return redirect()->route('register')->with('message', '¡Pago exitoso! Ahora crea tu cuenta.');
}


    public function cancel(Request $request)
{
    if (auth()->check()) {
        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }

    return redirect('/')->with('message', 'Pago cancelado. No se ha iniciado sesión.');
}

}
