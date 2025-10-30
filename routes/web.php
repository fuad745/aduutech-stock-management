<?php

use Illuminate\Support\Facades\Route;
use App\Models\Product;

Route::get('/', function () {
    $products = Product::query()
        ->latest('updated_at')
        ->take(12)
        ->get();

    $brands = Product::query()
        ->select('brand')
        ->whereNotNull('brand')
        ->distinct()
        ->orderBy('brand')
        ->pluck('brand');

    return view('welcome', compact('products', 'brands'));
});
