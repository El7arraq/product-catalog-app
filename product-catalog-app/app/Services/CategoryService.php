<?php
namespace App\Services;

use App\Repositories\CategoryRepositoryInterface;

class CategoryService
{
    protected $categoryRepository;

    public function __construct(CategoryRepositoryInterface $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function listCategories()
    {
        return $this->categoryRepository->all();
    }

    public function createCategory(array $data)
    {
        $validation = $this->validateCategory($data);
        if ($validation !== true) {
            return ['errors' => $validation];
        }
        return $this->categoryRepository->create($data);
    }

    public function updateCategory($id, array $data)
    {
        $validation = $this->validateCategory($data, true);
        if ($validation !== true) {
            return ['errors' => $validation];
        }
        return $this->categoryRepository->update($id, $data);
    }

    private function validateCategory(array $data, $isUpdate = false)
    {
        $rules = [
            'name' => $isUpdate ? 'sometimes|required|string|max:255' : 'required|string|max:255',
            'parent_id' => 'nullable|integer|exists:categories,id',
        ];
        $validator = \Validator::make($data, $rules);
        return $validator->fails() ? $validator->errors() : true;
    }

    public function deleteCategory($id)
    {
        return $this->categoryRepository->delete($id);
    }

    public function findCategory($id)
    {
        return $this->categoryRepository->find($id);
    }
}
