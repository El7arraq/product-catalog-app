<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Services\CategoryService;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    private CategoryService $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    public function index(): JsonResponse
    {
        $categories = $this->categoryService->listCategories();
        return response()->json($categories);
    }

    public function store(StoreCategoryRequest $request): JsonResponse
    {
        $data = $request->only(['name', 'parent_id']);
        $result = $this->categoryService->createCategory($data);
        if (isset($result['errors'])) {
            return response()->json(['errors' => $result['errors']], 422);
        }
        
        return response()->json($result, 201);
    }

    public function show(int $id): JsonResponse
    {
        $category = $this->categoryService->findCategory($id);
        return response()->json($category);
    }

    public function update(UpdateCategoryRequest $request, int $id): JsonResponse
    {
        $data = $request->only(['name', 'parent_id']);
        $result = $this->categoryService->updateCategory($id, $data);
        if (isset($result['errors'])) {
            return response()->json(['errors' => $result['errors']], 422);
        }
        
        return response()->json($result);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->categoryService->deleteCategory($id);
        return response()->json(['message' => 'Category deleted successfully.']);
    }
}
