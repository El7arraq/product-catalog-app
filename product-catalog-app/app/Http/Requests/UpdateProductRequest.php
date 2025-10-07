<?php
namespace App\Http\Requests;

use App\Repositories\CategoryRepositoryInterface;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
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
            'name' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'price' => 'sometimes|required|numeric|min:0',
            'image' => 'nullable|file|image|max:2048',
            'categories' => 'sometimes|array',
            'categories.*' => 'integer',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($this->has('categories') && is_array($this->input('categories'))) {
                foreach ($this->input('categories') as $categoryId) {
                    try {
                        $this->categoryRepository->find($categoryId);
                    } catch (\Exception $e) {
                        $validator->errors()->add('categories', "Category with ID {$categoryId} does not exist.");
                    }
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Product name is required when provided.',
            'description.required' => 'Product description is required when provided.',
            'price.required' => 'Product price is required when provided.',
            'price.numeric' => 'Product price must be a valid number.',
            'price.min' => 'Product price cannot be negative.',
            'categories.array' => 'Categories must be provided as an array.',
        ];
    }
}
