<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\ProductService;
use Illuminate\Support\Facades\Validator;


class ProductController extends Controller
{
    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function index(Request $request)
    {
        $sortBy = $request->query('sortBy');
        $categoryId = $request->query('categoryId');
        $products = $this->productService->listProducts($sortBy, $categoryId);
        return response()->json($products);
    }

    public function store(Request $request)
    {
        $data = $request->only(['name', 'description', 'price', 'categories']);
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $path = $image->store('products', 'public');
            $data['image'] = $path;
        }
        $result = $this->productService->createProduct($data);
        if (isset($result['errors'])) {
            return response()->json(['errors' => $result['errors']], 422);
        }
        if ($request->has('categories')) {
            $result->categories()->sync($request->input('categories'));
        }
        return response()->json($result, 201);
    }

    public function show($id)
    {
        $product = $this->productService->findProduct($id);
        return response()->json($product);
    }

    public function update(Request $request, $id)
    {
        $data = $request->only(['name', 'description', 'price', 'image', 'categories']);
        $result = $this->productService->updateProduct($id, $data);
        if (isset($result['errors'])) {
            return response()->json(['errors' => $result['errors']], 422);
        }
        if ($request->has('categories')) {
            $result->categories()->sync($request->input('categories'));
        }
        return response()->json($result);
    }

    public function destroy($id)
    {
        $this->productService->deleteProduct($id);
        return response()->json(['message' => 'Product deleted successfully.']);
    }
}
