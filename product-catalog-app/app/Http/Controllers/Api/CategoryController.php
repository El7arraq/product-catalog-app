<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\CategoryService;
use Illuminate\Support\Facades\Validator;


class CategoryController extends Controller
{
    protected $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    public function index()
    {
        $categories = $this->categoryService->listCategories();
        return response()->json($categories);
    }

    public function store(Request $request)
    {
        $data = $request->only(['name', 'parent_id']);
        $result = $this->categoryService->createCategory($data);
        if (isset($result['errors'])) {
            return response()->json(['errors' => $result['errors']], 422);
        }
        return response()->json($result, 201);
    }

    public function show($id)
    {
        $category = $this->categoryService->findCategory($id);
        return response()->json($category);
    }

    public function update(Request $request, $id)
    {
        $data = $request->only(['name', 'parent_id']);
        $result = $this->categoryService->updateCategory($id, $data);
        if (isset($result['errors'])) {
            return response()->json(['errors' => $result['errors']], 422);
        }
        return response()->json($result);
    }

    public function destroy($id)
    {
        $this->categoryService->deleteCategory($id);
        return response()->json(['message' => 'Category deleted successfully.']);
    }
}
