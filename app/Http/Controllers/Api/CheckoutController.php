<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Services\OrderService;

class CheckoutController extends Controller
{
    public function __construct(private OrderService $orderService) {}

    public function checkout(Request $request)
    {
        $data = $request->validate([
            'full_name' => 'required|string',
            'phone' => 'required|string',
            'street' => 'required|string',
            'city' => 'required|string',
            'country' => 'required|string',
            'postal_code' => 'nullable|string',
            'payment_method' => 'required|in:cod,card'
        ]);

        try {
            $order = $this->orderService->checkout(
                $request->user(),
                $data
            );

            return response()->json($order, 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }
    }
}