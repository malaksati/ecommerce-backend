<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
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

        return OrderResource::collection($orders);
    }
    public function show($id)
    {
        $order = Order::with(['items.product', 'user'])
            ->findOrFail($id);

        return new OrderResource($order);
    }
    public function updateStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        $data = $request->validate([
            'status' => 'required|in:pending,confirmed,processing,shipped,delivered,cancelled,refunded'
        ]);

        // ❗ prevent invalid transitions (optional advanced)
        if ($order->status === 'delivered') {
            return response()->json(['message' => 'Cannot modify delivered order'], 400);
        }

        $order->update(['status' => $data['status']]);

        return response()->json([
            'message' => 'Order status updated',   // 👈 add this
            'order'   => new OrderResource($order),
        ]);
    }
    public function updatePayment(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        $data = $request->validate([
            'payment_status' => 'required|in:unpaid,paid,refunded'
        ]);

        $order->update([
            'payment_status' => $data['payment_status']
        ]);

        $order->update(['payment_status' => $data['payment_status']]);

        return response()->json([
            'message' => 'Payment status updated',  // 👈 add this
            'order'   => new OrderResource($order),
        ]);
    }
    public function stats()
    {
        return response()->json([
            'total_orders'  => Order::count(),
            'pending'       => Order::where('status', 'pending')->count(),
            'processing'    => Order::where('status', 'processing')->count(),
            'delivered'     => Order::where('status', 'delivered')->count(),
            'cancelled'     => Order::where('status', 'cancelled')->count(),
            'total_revenue' => Order::where('payment_status', 'paid')->sum('total'),
        ]);
    }
}
