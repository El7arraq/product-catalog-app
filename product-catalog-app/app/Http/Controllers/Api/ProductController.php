<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\ProductService;
use App\Services\FileUploadServiceInterface;
use Illuminate\Support\Facades\Validator;


class ProductController extends Controller
{
    protected $productService;
    protected $fileUploadService;

    public function __construct(ProductService $productService, FileUploadServiceInterface $fileUploadService)
    {
        $this->productService = $productService;
        $this->fileUploadService = $fileUploadService;
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
        
        // Handle image upload using dedicated service
        if ($request->hasFile('image')) {
            try {
                $imagePath = $this->fileUploadService->uploadImage($request->file('image'), 'products');
                $data['image'] = $imagePath;
            } catch (\InvalidArgumentException $e) {
                return response()->json(['error' => $e->getMessage()], 422);
            }
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
        
        // Handle image upload using dedicated service
        if ($request->hasFile('image')) {
            try {
                $imagePath = $this->fileUploadService->uploadImage($request->file('image'), 'products');
                $data['image'] = $imagePath;
            } catch (\InvalidArgumentException $e) {
                return response()->json(['error' => $e->getMessage()], 422);
            }
        }
        
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
