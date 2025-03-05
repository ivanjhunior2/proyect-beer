<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use App\Models\Order;
use App\Models\Payment;
use Stripe\Charge;
use App\Http\Requests\PaymentRequest;
use App\Http\Resources\PaymentResource;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function __construct(){
        Stripe::setApiKey(env('STRIPE_SECRET_KEY'));
    }

    public function index()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PaymentRequest $request)
    {
        $order = Order::findOrFail($request->order_id);

        if ($order->total != $request->amount) {
            return response()->json(['message' => 'Monto incorrecto'], 400);
        }

        try {
            // Crear un cargo en Stripe
            $charge = Charge::create([
                'amount' => $request->amount * 100, // Stripe maneja centavos
                'currency' => 'usd',
                'source' => $request->token,        // Token enviado desde el frontend
                'description' => 'Pago del pedido #' . $order->id,
            ]);

            // Crear el registro de pago en la BD
            $payment = Payment::create([
                'order_id' => $order->id,
                'amount' => $request->amount,
                'status' => $charge->status,
                'payment_method' => 'card',
                'transaction_id' => $charge->id
            ]);

            // Actualizar el estado del pedido
            if ($charge->status === 'succeeded') {
                $order->update(['status' => 'confirmed']);
            }

            return new PaymentResource($payment);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error en el pago: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Payment  $payment)
    {
        return new PaymentResource($payment);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
