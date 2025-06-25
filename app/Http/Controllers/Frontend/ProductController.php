<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;

class ProductController extends Controller
{
    // ✅ Halaman semua produk dengan filter kategori
    public function collection(Request $request)
    {
        $query = Product::with('category');

        // Filter berdasarkan kategori jika ada
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }
        // Filter by harga
        if ($request->filled('price_min') && $request->filled('price_max')) {
            $query->whereBetween('price', [$request->price_min, $request->price_max]);
        }

        $products = $query->latest()->get();

        // Ambil semua kategori dan jumlah produk tiap kategori
        $categories = Category::withCount('products')->get();

        return view('frontend.product.collection', compact('products', 'categories'));
    }

    // ✅ Halaman Home
    public function index()
    {
        $categories = Category::all();
        return view('frontend.home', compact('categories'));
    }

    // ✅ Detail Produk
   public function detail($slug)
{
    $product = Product::where('slug', $slug)->with('category')->firstOrFail();
    return view('frontend.product.detail', compact('product'));
}

}
