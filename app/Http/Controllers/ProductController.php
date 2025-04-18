<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    // GET /api/products
    public function index()
    {
        return Product::paginate();   // automatic JSON
    }

    // POST /api/products
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'             => 'required|string|max:120',
            'description'      => 'nullable|string',
            'image_url'        => 'nullable|url',
            'firmware_version' => 'nullable|string|max:20',
            'is_active'        => 'boolean',
        ]);

        return Product::create($validated);
    }

    // GET /api/products/{product}
    public function show(Product $product)
    {
        return $product;
    }

    // PUT/PATCH /api/products/{product}
    public function update(Request $request, Product $product)
    {
        $product->update($request->validate([
            'name'             => 'sometimes|required|string|max:120',
            'description'      => 'nullable|string',
            'image_url'        => 'nullable|url',
            'firmware_version' => 'nullable|string|max:20',
            'is_active'        => 'boolean',
        ]));

        return $product->refresh();
    }

    // DELETE /api/products/{product}
    public function destroy(Product $product)
    {
        $product->delete();
        return response()->noContent();
    }
}
