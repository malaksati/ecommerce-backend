<?php

namespace App\Services;

use App\Models\Product;
use App\Models\CartItem;
use Illuminate\Support\Facades\DB;

class CartService
{
    // 🧾 Get Cart
    public function getCart($user)
    {
        $items = CartItem::with(['product.primaryImage'])
            ->where('user_id', $user->id)
            ->get();

        $total = $items->sum(fn ($item) => $item->subtotal);

        return [
            'items' => $items,
            'total' => $total,
        ];
    }

    // ➕ Add to Cart
    public function add($user, $productId, $qty)
    {
        return DB::transaction(function () use ($user, $productId, $qty) {

            $product = Product::findOrFail($productId);

            // ❗ Active check
            if (!$product->is_active) {
                throw new \Exception('Product unavailable');
            }

            $cartItem = CartItem::where('user_id', $user->id)
                ->where('product_id', $productId)
                ->first();

            $newQty = ($cartItem->quantity ?? 0) + $qty;

            // ❗ Stock check
            if ($newQty > $product->stock) {
                throw new \Exception('Stock exceeded');
            }

            if ($cartItem) {
                $cartItem->update(['quantity' => $newQty]);
            } else {
                $cartItem = CartItem::create([
                    'user_id' => $user->id,
                    'product_id' => $productId,
                    'quantity' => $qty
                ]);
            }

            return $cartItem;
        });
    }

    // 🔄 Update quantity
    public function update($user, $cartItemId, $qty)
    {
        return DB::transaction(function () use ($user, $cartItemId, $qty) {

            $cartItem = CartItem::where('user_id', $user->id)
                ->findOrFail($cartItemId);

            $product = $cartItem->product;

            if (!$product->is_active) {
                throw new \Exception('Product unavailable');
            }

            // ❗ Remove if zero
            if ($qty == 0) {
                $cartItem->delete();
                return null;
            }

            if ($qty > $product->stock) {
                throw new \Exception('Stock exceeded');
            }

            $cartItem->update(['quantity' => $qty]);

            return $cartItem;
        });
    }

    // ❌ Remove item
    public function remove($user, $cartItemId)
    {
        $cartItem = CartItem::where('user_id', $user->id)
            ->findOrFail($cartItemId);

        $cartItem->delete();
    }

    // 🧹 Clear cart
    public function clear($user)
    {
        CartItem::where('user_id', $user->id)->delete();
    }
}