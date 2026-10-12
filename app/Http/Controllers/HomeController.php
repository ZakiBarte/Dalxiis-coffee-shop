<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('q', ''));
        $categoryId = $request->query('category');

        $products = Product::with('category')
            ->where('is_available', true)
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($w) use ($search) {
                    $w->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->when($categoryId, fn ($query) => $query->where('category_id', $categoryId))
            ->orderBy('name')
            ->get()
            ->groupBy(fn ($product) => $product->category->name);

        $categories = Category::orderBy('name')->get();

        return view('home', compact('products', 'categories', 'search', 'categoryId'));
    }
}
