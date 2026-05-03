<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryFilterController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::where('is_active', true)
            ->get();

        return response()->json($categories);
    }
}