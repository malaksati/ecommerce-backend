<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        return Category::with('children')->whereNull('parent_id')->get();
    }
    public function show(Category $category)
    {
        return $category->load('children');
    }
}
