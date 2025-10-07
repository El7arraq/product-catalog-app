<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Services\FileUploadServiceInterface;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    private ProductService $productService;
    private FileUploadServiceInterface $fileUploadService;

    public function __construct(
        ProductService $productService,
        FileUploadServiceInterface $fileUploadService
    ) {
        $this->productService = $productService;
        $this->fileUploadService = $fileUploadService;
    }

    public function index(Request $request): JsonResponse
    {
        $sortBy = $request->query('sortBy');
        $categoryId = $request->query('categoryId') ? (int) $request->query('categoryId') : null;
        $products = $this->productService->listProducts($sortBy, $categoryId);
        
        return response()->json($products);
    }

    public function store(StoreProductRequest $request): JsonResponse
    {
        $data = $request->only(['name', 'description', 'price', 'categories']);
        
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
        
        return response()->json($result, 201);
    }

    public function show(int $id): JsonResponse
    {
        $product = $this->productService->findProduct($id);
        return response()->json($product);
    }

    public function update(UpdateProductRequest $request, int $id): JsonResponse
    {
        $data = $request->only(['name', 'description', 'price', 'image', 'categories']);
        
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
        
        return response()->json($result);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->productService->deleteProduct($id);
        return response()->json(['message' => 'Product deleted successfully.']);
    }
}
