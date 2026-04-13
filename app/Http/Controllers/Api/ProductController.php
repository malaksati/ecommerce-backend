<?php

namespace App\Http\Controllers\Api;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProductStoreRequest;
use App\Http\Resources\ProductResource;
use App\Models\Category;
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
            $allowedSorts = ['price', 'name', 'created_at', 'stock'];
            if (in_array($request->sort_by, $allowedSorts)) {
                $allowedDirs = ['asc', 'desc'];
                $dir = in_array($request->sort_dir, $allowedDirs) ? $request->sort_dir : 'asc';
                $query->orderBy($request->sort_by, $dir);
            }
        } else {
            $query->latest();
        }

        $products = $query->paginate(10);

        return ProductResource::collection($products);
    }

    public function grouped()
    {
        $categories = Category::with(['products' => function ($query) {
            $query->where('is_active', true)
                ->with('images')
                ->orderBy('name', 'asc');  // alphabetic order
        }])
            ->whereHas('products', fn($q) => $q->where('is_active', true))
            ->orderBy('name', 'asc')  // categories alphabetic too
            ->get();

        return response()->json($categories);
    }
    // 🔹 GET /products/{slug}
    public function show($slug)
    {
        $product = Product::with(['category', 'images'])
            ->where('slug', $slug)
            ->firstOrFail();

        return new ProductResource($product);
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
                    'image_url' => $path,  // ✅
                    'is_primary' => $index === 0,
                ]);
            }
        }

        return response()->json($product->load('images'), 201);
    }

    // 🔹 PUT /products/{id}
    public function update(ProductStoreRequest $request, $id)
    {
        $product = Product::findOrFail($id);

        $validated = $request->validated();

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
