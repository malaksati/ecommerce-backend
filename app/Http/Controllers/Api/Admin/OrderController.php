<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = Order::with('user')
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(15);

        return response()->json($orders);
    }
    public function show($id)
    {
        $order = Order::with(['items.product', 'user'])
            ->findOrFail($id);

        return response()->json($order);
    }
    public function updateStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        $data = $request->validate([
            'status' => 'required|in:pending,confirmed,shipped,delivered,cancelled'
        ]);

        // ❗ prevent invalid transitions (optional advanced)
        if ($order->status === 'delivered') {
            return response()->json(['message' => 'Cannot modify delivered order'], 400);
        }

        $order->update(['status' => $data['status']]);

        return response()->json($order);
    }
    public function updatePayment(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        $data = $request->validate([
            'payment_status' => 'required|in:pending,paid,failed'
        ]);

        $order->update([
            'payment_status' => $data['payment_status']
        ]);

        return response()->json($order);
    }
}
