<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * @OA\Get(
     *     path="/products",
     *     summary="List products",
     *     description="Returns paginated list of smart bed products",
     *     operationId="listProducts",
     *     tags={"Products"},
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number",
     *         required=false,
     *         @OA\Schema(type="integer", format="int64")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of products",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array", @OA\Items(
     *                 @OA\Property(property="id", type="string", format="uuid"),
     *                 @OA\Property(property="name", type="string", example="SmartRest Pro Series"),
     *                 @OA\Property(property="description", type="string"),
     *                 @OA\Property(property="image_url", type="string", format="uri"),
     *                 @OA\Property(property="firmware_version", type="string"),
     *                 @OA\Property(property="is_active", type="boolean"),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time")
     *             )),
     *             @OA\Property(property="current_page", type="integer"),
     *             @OA\Property(property="total", type="integer"),
     *             @OA\Property(property="per_page", type="integer")
     *         )
     *     )
     * )
     */
    // GET /api/products
    public function index()
    {
        return Product::paginate();   // automatic JSON
    }

    /**
     * @OA\Post(
     *     path="/products",
     *     summary="Create product",
     *     description="Create a new product in the catalog",
     *     operationId="createProduct",
     *     tags={"Products"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example="SmartRest Elite Mattress"),
     *             @OA\Property(property="description", type="string", example="Advanced smart mattress with sleep tracking"),
     *             @OA\Property(property="image_url", type="string", format="uri", example="https://example.com/images/product.jpg"),
     *             @OA\Property(property="firmware_version", type="string", example="1.2.0"),
     *             @OA\Property(property="is_active", type="boolean", example=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Product created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="string", format="uuid"),
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="description", type="string"),
     *             @OA\Property(property="image_url", type="string", format="uri"),
     *             @OA\Property(property="firmware_version", type="string"),
     *             @OA\Property(property="is_active", type="boolean"),
     *             @OA\Property(property="created_at", type="string", format="date-time"),
     *             @OA\Property(property="updated_at", type="string", format="date-time")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation errors",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
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

    /**
     * @OA\Get(
     *     path="/products/{product}",
     *     summary="Show product",
     *     description="Display details of a specific product",
     *     operationId="getProduct",
     *     tags={"Products"},
     *     @OA\Parameter(
     *         name="product",
     *         in="path",
     *         description="Product ID",
     *         required=true,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product details",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="string", format="uuid"),
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="description", type="string"),
     *             @OA\Property(property="image_url", type="string", format="uri"),
     *             @OA\Property(property="firmware_version", type="string"),
     *             @OA\Property(property="is_active", type="boolean"),
     *             @OA\Property(property="created_at", type="string", format="date-time"),
     *             @OA\Property(property="updated_at", type="string", format="date-time")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product not found"
     *     )
     * )
     */
    // GET /api/products/{product}
    public function show(Product $product)
    {
        return $product;
    }

    /**
     * @OA\Put(
     *     path="/products/{product}",
     *     summary="Update product",
     *     description="Update an existing product",
     *     operationId="updateProduct",
     *     tags={"Products"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="product",
     *         in="path",
     *         description="Product ID",
     *         required=true,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="SmartRest Elite Mattress v2"),
     *             @OA\Property(property="description", type="string", example="Updated description"),
     *             @OA\Property(property="image_url", type="string", format="uri", example="https://example.com/images/product_v2.jpg"),
     *             @OA\Property(property="firmware_version", type="string", example="1.3.0"),
     *             @OA\Property(property="is_active", type="boolean", example=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="string", format="uuid"),
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="description", type="string"),
     *             @OA\Property(property="image_url", type="string", format="uri"),
     *             @OA\Property(property="firmware_version", type="string"),
     *             @OA\Property(property="is_active", type="boolean"),
     *             @OA\Property(property="created_at", type="string", format="date-time"),
     *             @OA\Property(property="updated_at", type="string", format="date-time")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product not found"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation errors",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
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

    /**
     * @OA\Delete(
     *     path="/products/{product}",
     *     summary="Delete product",
     *     description="Delete an existing product",
     *     operationId="deleteProduct",
     *     tags={"Products"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="product",
     *         in="path",
     *         description="Product ID",
     *         required=true,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Product deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Unauthorized to delete this product",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthorized")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product not found"
     *     )
     * )
     */
    // DELETE /api/products/{product}
    public function destroy(Product $product)
    {
        $product->delete();
        return response()->noContent();
    }
}
