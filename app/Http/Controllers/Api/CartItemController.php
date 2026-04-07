<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\CartService;

class CartItemController extends Controller
{
    public function __construct(private CartService $cartService) {}

    public function index(Request $request)
    {
        return response()->json(
            $this->cartService->getCart($request->user())
        );
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
            'qty' => 'required|integer|min:1'
        ]);

        try {
            $item = $this->cartService->add(
                $request->user(),
                $data['product_id'],
                $data['qty']
            );

            return response()->json($item, 201);

        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'qty' => 'required|integer|min:0'
        ]);

        try {
            $item = $this->cartService->update(
                $request->user(),
                $id,
                $data['qty']
            );

            return response()->json($item ?? ['message' => 'Item removed']);

        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function destroy(Request $request, $id)
    {
        $this->cartService->remove($request->user(), $id);

        return response()->json(['message' => 'Removed']);
    }

    public function clear(Request $request)
    {
        $this->cartService->clear($request->user());

        return response()->json(['message' => 'Cart cleared']);
    }
}