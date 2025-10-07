<?php
namespace App\Services;

use App\Repositories\CategoryRepositoryInterface;
use Illuminate\Database\Eloquent\Model;

class CategoryService
{
    private CategoryRepositoryInterface $categoryRepository;
    private ValidationService $validationService;

    public function __construct(
        CategoryRepositoryInterface $categoryRepository,
        ValidationService $validationService
    ) {
        $this->categoryRepository = $categoryRepository;
        $this->validationService = $validationService;
    }

    public function listCategories(): iterable
    {
        return $this->categoryRepository->all();
    }

    public function createCategory(array $data): array|Model
    {
        $validation = $this->validationService->validateCategoryData($data);
        if ($validation !== true) {
            return ['errors' => $validation];
        }
        
        return $this->categoryRepository->create($data);
    }

    public function updateCategory(int $id, array $data): array|Model
    {
        $validation = $this->validationService->validateCategoryData($data, true);
        if ($validation !== true) {
            return ['errors' => $validation];
        }
        
        return $this->categoryRepository->update($id, $data);
    }

    public function deleteCategory(int $id): bool
    {
        return $this->categoryRepository->delete($id);
    }

    public function findCategory(int $id): Model
    {
        return $this->categoryRepository->find($id);
    }
}
