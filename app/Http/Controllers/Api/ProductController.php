<?php

namespace App\Http\Controllers\Api;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProductStoreRequest;
use App\Services\ImageService;

class ProductController extends Controller
{
    // 🔹 GET /products (list + filters + search)
    public function index(Request $request)
    {
        $query = Product::with(['category', 'images']);

        // 🔍 Search
        if ($request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // 📂 Filter by category
        if ($request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        // 💰 Price range
        if ($request->min_price) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->max_price) {
            $query->where('price', '<=', $request->max_price);
        }

        // 📦 In stock filter
        if ($request->in_stock) {
            $query->where('stock', '>', 0);
        }

        // 🔽 Sorting
        if ($request->sort_by) {
            $query->orderBy($request->sort_by, $request->sort_dir ?? 'asc');
        } else {
            $query->latest();
        }

        $products = $query->paginate(10);

        return response()->json($products);
    }

    // 🔹 GET /products/{slug}
    public function show(Product $product)
    {
        $product = Product::with(['category', 'images'])
            ->where('slug', $product->slug)
            ->firstOrFail();

        return response()->json($product);
    }

    // 🔹 POST /products (Admin)
    public function store(ProductStoreRequest $request, ImageService $imageService)
    {
        $product = Product::create($request->validated());
        // 📸 Upload images
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $file) {

                $path = $imageService->uploadProductImage($file);

                $product->images()->create([
                    'url' => $path,
                    'is_primary' => $index === 0, // first image primary
                ]);
            }
        }

        return response()->json($product->load('images'), 201);
    }

    // 🔹 PUT /products/{id}
    public function update(ProductStoreRequest $request, $id)
    {
        $product = Product::findOrFail($id);

        $validated = $request->validate();

        if (isset($validated['name'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $product->update($validated);

        return response()->json($product);
    }

    // 🔹 DELETE /products/{id}
    public function destroy($id)
    {
        Product::findOrFail($id)->delete();

        return response()->json(['message' => 'Deleted']);
    }
}
