<?php
namespace App\Services;

use App\Repositories\ProductRepositoryInterface;

class ProductService
{
    protected $productRepository;

    public function __construct(ProductRepositoryInterface $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function listProducts($sortBy = null, $categoryId = null)
    {
        return $this->productRepository->all($sortBy, $categoryId);
    }

    public function createProduct(array $data)
    {
        $validation = $this->validateProduct($data);
        if ($validation !== true) {
            return ['errors' => $validation];
        }
        return $this->productRepository->create($data);
    }

    public function updateProduct($id, array $data)
    {
        $validation = $this->validateProduct($data, true);
        if ($validation !== true) {
            return ['errors' => $validation];
        }
        return $this->productRepository->update($id, $data);
    }

    private function validateProduct(array $data, $isUpdate = false)
    {
        $rules = [
            'name' => $isUpdate ? 'sometimes|required|string|max:255' : 'required|string|max:255',
            'description' => $isUpdate ? 'sometimes|required|string' : 'required|string',
            'price' => $isUpdate ? 'sometimes|required|numeric' : 'required|numeric',
            'image' => 'nullable|string',
            'categories' => 'array',
            'categories.*' => 'integer|exists:categories,id',
        ];
        $validator = \Validator::make($data, $rules);
        return $validator->fails() ? $validator->errors() : true;
    }

    public function deleteProduct($id)
    {
        return $this->productRepository->delete($id);
    }

    public function findProduct($id)
    {
        return $this->productRepository->find($id);
    }
}
