<?php
namespace App\Http\Requests;

use App\Repositories\CategoryRepositoryInterface;
use Illuminate\Foundation\Http\FormRequest;

class StoreCategoryRequest extends FormRequest
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
        return [
            'name' => 'required|string|max:255|unique:categories,name',
            'parent_id' => 'nullable|integer',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($this->filled('parent_id')) {
                try {
                    $this->categoryRepository->find($this->input('parent_id'));
                } catch (\Exception $e) {
                    $validator->errors()->add('parent_id', 'The selected parent category does not exist.');
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Category name is required.',
            'name.unique' => 'A category with this name already exists.',
            'parent_id.integer' => 'Parent category must be a valid category ID.',
        ];
    }
}
