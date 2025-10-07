<?php
namespace App\Services;

use App\Repositories\ProductRepositoryInterface;
use Illuminate\Database\Eloquent\Model;

class ProductService
{
    private ProductRepositoryInterface $productRepository;
    private ValidationService $validationService;

    public function __construct(
        ProductRepositoryInterface $productRepository,
        ValidationService $validationService
    ) {
        $this->productRepository = $productRepository;
        $this->validationService = $validationService;
    }

    public function listProducts(?string $sortBy = null, ?int $categoryId = null): iterable
    {
        return $this->productRepository->all($sortBy, $categoryId);
    }

    public function createProduct(array $data): array|Model
    {
        $validation = $this->validationService->validateProductData($data);
        if ($validation !== true) {
            return ['errors' => $validation];
        }
        
        if (!isset($data['image'])) {
            $data['image'] = null;
        }
        
        return $this->productRepository->createWithCategories($data);
    }

    public function updateProduct(int $id, array $data): array|Model
    {
        $validation = $this->validationService->validateProductData($data, true);
        if ($validation !== true) {
            return ['errors' => $validation];
        }
        
        return $this->productRepository->updateWithCategories($id, $data);
    }

    public function deleteProduct(int $id): bool
    {
        return $this->productRepository->delete($id);
    }

    public function findProduct(int $id): Model
    {
        return $this->productRepository->find($id);
    }
}
