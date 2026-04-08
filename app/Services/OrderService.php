<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use App\Models\CartItem;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function checkout($user, array $data)
    {
        return DB::transaction(function () use ($user, $data) {

            // 🛒 Get cart
            $cartItems = CartItem::with('product')
                ->where('user_id', $user->id)
                ->get();

            if ($cartItems->isEmpty()) {
                throw new \Exception('Cart is empty');
            }

            $subtotal = 0;
            $orderItemsData = [];

            foreach ($cartItems as $item) {
                $product = $item->product;

                // ❗ Check product status
                if (!$product->is_active) {
                    throw new \Exception("Product {$product->name} unavailable");
                }

                // ❗ Check stock
                if ($item->quantity > $product->stock) {
                    throw new \Exception("Stock not enough for {$product->name}");
                }

                $price = $product->current_price;
                $lineTotal = $price * $item->quantity;

                $subtotal += $lineTotal;

                $orderItemsData[] = [
                    'product_id'   => $product->id,
                    'product_name' => $product->name,
                    'product_sku'  => $product->sku,
                    'unit_price'   => $price,
                    'quantity'     => $item->quantity,
                    'subtotal'     => $lineTotal,
                ];
            }

            // 🚚 Simple shipping logic
            $shippingCost = $subtotal > 1000 ? 0 : 50;

            // 🎁 Discount (placeholder)
            $discount = 0;

            $total = $subtotal + $shippingCost - $discount;

            // 🧾 Create order
            $order = Order::create([
                'user_id' => $user->id,

                'shipping_full_name' => $data['full_name'],
                'shipping_phone'     => $data['phone'],
                'shipping_street'    => $data['street'],
                'shipping_city'      => $data['city'],
                'shipping_country'   => $data['country'],
                'shipping_postal_code' => $data['postal_code'] ?? null,

                'status' => 'pending',
                'payment_method' => $data['payment_method'],
                'payment_status' => 'unpaid',

                'subtotal' => $subtotal,
                'shipping_cost' => $shippingCost,
                'discount' => $discount,
                'total' => $total,
            ]);

            // 📦 Create order items
            foreach ($orderItemsData as $item) {
                $order->items()->create($item);
            }

            // 📉 Reduce stock
            foreach ($cartItems as $item) {
                $product = $item->product;
                $product->decrement('stock', $item->quantity);
            }

            // 🧹 Clear cart
            CartItem::where('user_id', $user->id)->delete();

            return $order->load('items');
        });
    }
}
