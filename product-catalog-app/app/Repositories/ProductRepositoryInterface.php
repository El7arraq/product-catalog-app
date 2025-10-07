<?php
namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;

interface ProductRepositoryInterface
{
    public function all(?string $sortBy = null, ?int $categoryId = null): iterable;
    
    public function find(int $id): Model;
    
    public function create(array $data): Model;
    
    public function createWithCategories(array $data): Model;
    
    public function update(int $id, array $data): Model;
    
    public function updateWithCategories(int $id, array $data): Model;
    
    public function delete(int $id): bool;
}
