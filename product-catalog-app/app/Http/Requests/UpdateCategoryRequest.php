<?php
namespace App\Http\Requests;

use App\Repositories\CategoryRepositoryInterface;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCategoryRequest extends FormRequest
{
    private CategoryRepositoryInterface $categoryRepository;

    public function __construct(CategoryRepositoryInterface $categoryRepository)
    {
        parent::__construct();
        $this->categoryRepository = $categoryRepository;
    }

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $categoryId = $this->route('category');
        
        return [
            'name' => "sometimes|required|string|max:255|unique:categories,name,{$categoryId}",
            'parent_id' => 'nullable|integer',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($this->filled('parent_id')) {
                $currentCategoryId = $this->route('category');
                $parentId = $this->input('parent_id');
                
                if ($currentCategoryId == $parentId) {
                    $validator->errors()->add('parent_id', 'A category cannot be its own parent.');
                    return;
                }
                
                try {
                    $this->categoryRepository->find($parentId);
                } catch (\Exception $e) {
                    $validator->errors()->add('parent_id', 'The selected parent category does not exist.');
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Category name is required when provided.',
            'name.unique' => 'A category with this name already exists.',
            'parent_id.integer' => 'Parent category must be a valid category ID.',
        ];
    }
}
