<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cart\AddToCartRequest;
use App\Http\Requests\Cart\UpdateCartRequest;
use App\Http\Resources\CartItemResource;
use App\Services\CartService;
use Illuminate\Http\Request;

class CartItemController extends Controller
{
    public function __construct(private CartService $cartService) {}

    // GET /cart
    public function index(Request $request)
    {
        $cart = $this->cartService->getCart($request->user());

        return response()->json([
            'items' => CartItemResource::collection($cart['items']),
            'total' => $cart['total'],
        ]);
    }

    // POST /cart
    public function store(AddToCartRequest $request)
    {
        try {
            $item = $this->cartService->add(
                $request->user(),
                $request->product_id,
                $request->quantity
            );

            return response()->json([
                'message' => 'Item added to cart',
                'item'    => new CartItemResource($item),
            ], 201);

        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    // PUT /cart/{id}
    public function update(UpdateCartRequest $request, $id)
    {
        try {
            $item = $this->cartService->update(
                $request->user(),
                $id,
                $request->quantity
            );

            return response()->json(
                $item
                    ? ['message' => 'Cart updated', 'item' => new CartItemResource($item)]
                    : ['message' => 'Item removed']
            );

        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    // DELETE /cart/{id}
    public function destroy(Request $request, $id)
    {
        $this->cartService->remove($request->user(), $id);

        return response()->json(['message' => 'Item removed']);
    }

    // DELETE /cart
    public function clear(Request $request)
    {
        $this->cartService->clear($request->user());

        return response()->json(['message' => 'Cart cleared']);
    }
}