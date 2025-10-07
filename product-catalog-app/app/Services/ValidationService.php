<?php
namespace App\Services;

use App\Repositories\CategoryRepositoryInterface;
use Illuminate\Contracts\Validation\Factory as ValidatorFactory;

class ValidationService
{
    private ValidatorFactory $validatorFactory;
    private CategoryRepositoryInterface $categoryRepository;

    public function __construct(
        ValidatorFactory $validatorFactory,
        CategoryRepositoryInterface $categoryRepository
    ) {
        $this->validatorFactory = $validatorFactory;
        $this->categoryRepository = $categoryRepository;
    }

    public function validateProductData(array $data, bool $isUpdate = false): bool|object
    {
        $rules = [
            'name' => $isUpdate ? 'sometimes|required|string|max:255' : 'required|string|max:255',
            'description' => $isUpdate ? 'sometimes|required|string' : 'required|string',
            'price' => $isUpdate ? 'sometimes|required|numeric|min:0' : 'required|numeric|min:0',
            'image' => 'nullable|string',
            'categories' => 'array',
            'categories.*' => 'integer',
        ];

        $validator = $this->validatorFactory->make($data, $rules);

        if ($validator->fails()) {
            return $validator->errors();
        }

        if (isset($data['categories']) && is_array($data['categories'])) {
            foreach ($data['categories'] as $categoryId) {
                try {
                    $this->categoryRepository->find($categoryId);
                } catch (\Exception $e) {
                    $validator->errors()->add('categories', "Category with ID {$categoryId} does not exist.");
                    return $validator->errors();
                }
            }
        }

        return true;
    }

    public function validateCategoryData(array $data, bool $isUpdate = false): bool|object
    {
        $rules = [
            'name' => $isUpdate ? 'sometimes|required|string|max:255' : 'required|string|max:255',
            'parent_id' => 'nullable|integer',
        ];

        $validator = $this->validatorFactory->make($data, $rules);

        if ($validator->fails()) {
            return $validator->errors();
        }

        if (isset($data['parent_id']) && $data['parent_id'] !== null) {
            try {
                $this->categoryRepository->find($data['parent_id']);
            } catch (\Exception $e) {
                $validator->errors()->add('parent_id', 'The selected parent category does not exist.');
                return $validator->errors();
            }
        }

        return true;
    }
}