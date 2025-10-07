<?php
namespace App\Repositories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Model;

class ProductRepository implements ProductRepositoryInterface
{
    public function all(?string $sortBy = null, ?int $categoryId = null): iterable
    {
        $query = Product::with('categories');
        $query->orderBy('price', $sortBy ?? 'desc');
        
        if ($categoryId) {
            $query->whereHas('categories', function ($q) use ($categoryId) {
                $q->where('categories.id', $categoryId);
            });
        }
        
        return $query->get();
    }

    public function find(int $id): Model
    {
        return Product::findOrFail($id);
    }

    public function create(array $data): Model
    {
        return Product::create($data);
    }

    public function createWithCategories(array $data): Model
    {
        $categories = $data['categories'] ?? [];
        unset($data['categories']);
        
        $product = Product::create($data);
        
        if (!empty($categories)) {
            $product->categories()->sync($categories);
        }
        
        return $product->load('categories');
    }

    public function update(int $id, array $data): Model
    {
        $product = $this->find($id);
        $product->update($data);
        return $product;
    }

    public function updateWithCategories(int $id, array $data): Model
    {
        $categories = $data['categories'] ?? [];
        unset($data['categories']);
        
        $product = $this->find($id);
        $product->update($data);
        
        if (!empty($categories)) {
            $product->categories()->sync($categories);
        }
        
        return $product->load('categories');
    }

    public function delete(int $id): bool
    {
        $product = $this->find($id);
        return $product->delete();
    }
}
