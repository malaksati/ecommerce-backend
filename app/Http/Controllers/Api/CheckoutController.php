<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CheckoutRequest;
use App\Http\Resources\OrderResource;
use App\Services\OrderService;

class CheckoutController extends Controller
{
    public function __construct(private OrderService $orderService) {}

    public function checkout(CheckoutRequest $request)
    {
        try {
            $order = $this->orderService->checkout(
                $request->user(),
                $request->validated()   // 👈 clean, already validated
            );

            return response()->json([
                'message' => 'Order placed successfully',
                'order'   => new OrderResource($order),
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }
    }
}