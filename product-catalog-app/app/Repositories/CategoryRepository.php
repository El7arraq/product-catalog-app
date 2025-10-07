<?php
namespace App\Repositories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Model;

class CategoryRepository implements CategoryRepositoryInterface
{
    public function all(): iterable
    {
        return Category::all();
    }

    public function find(int $id): Model
    {
        return Category::findOrFail($id);
    }

    public function create(array $data): Model
    {
        return Category::create($data);
    }

    public function update(int $id, array $data): Model
    {
        $category = $this->find($id);
        $category->update($data);
        return $category;
    }

    public function delete(int $id): bool
    {
        $category = $this->find($id);
        return $category->delete();
    }
}
