<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query();

        if ($search = $request->string('search')->toString()) {
            $query->where(function ($q) use ($search) {
                $q->where('product_name', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%")
                  ->orWhere('model', 'like', "%{$search}%");
            });
        }

        $sortBy = $request->get('sort_by', 'id');
        $sortDir = $request->get('sort_dir', 'desc') === 'asc' ? 'asc' : 'desc';
        if (in_array($sortBy, ['id','product_name','brand','model','price','quantity','type'])) {
            $query->orderBy($sortBy, $sortDir);
        }

        $perPage = (int) $request->get('per_page', 15);
        $perPage = $perPage > 0 && $perPage <= 100 ? $perPage : 15;

        return $query->paginate($perPage);
    }

    public function show(Product $product)
    {
        return $product;
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_name' => ['required','string','max:255'],
            'brand' => ['nullable','string','max:255'],
            'model' => ['nullable','string','max:255'],
            'price' => ['required','numeric','min:0'],
            'quantity' => ['required','integer','min:0'],
            'type' => ['nullable','string','max:255'],
        ]);

        // Normalize nullable strings to empty strings to satisfy non-nullable DB columns
        $validated['brand'] = $validated['brand'] ?? '';
        $validated['model'] = $validated['model'] ?? '';
        $validated['type'] = $validated['type'] ?? '';

        $product = Product::create($validated);

        return response()->json($product, Response::HTTP_CREATED);
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'product_name' => ['sometimes','required','string','max:255'],
            'brand' => ['sometimes','nullable','string','max:255'],
            'model' => ['sometimes','nullable','string','max:255'],
            'price' => ['sometimes','required','numeric','min:0'],
            'quantity' => ['sometimes','required','integer','min:0'],
            'type' => ['sometimes','nullable','string','max:255'],
        ]);

        // Normalize nullable strings to empty strings for non-nullable DB columns
        if (array_key_exists('brand', $validated)) {
            $validated['brand'] = $validated['brand'] ?? '';
        }
        if (array_key_exists('model', $validated)) {
            $validated['model'] = $validated['model'] ?? '';
        }
        if (array_key_exists('type', $validated)) {
            $validated['type'] = $validated['type'] ?? '';
        }

        $product->update($validated);

        return $product;
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return response()->noContent();
    }
}
