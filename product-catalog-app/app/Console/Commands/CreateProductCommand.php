<?php
namespace App\Console\Commands;

use App\Services\ProductService;
use Illuminate\Console\Command;

class CreateProductCommand extends Command
{
    protected $signature = 'product:create';
    protected $description = 'Create a new product via CLI';

    private ProductService $productService;

    public function __construct(ProductService $productService)
    {
        parent::__construct();
        $this->productService = $productService;
    }

    public function handle(): int
    {
        $name = $this->ask('Product name');
        $description = $this->ask('Product description');
        $price = $this->ask('Product price');
        $image = $this->ask('Product image URL (optional)');

        $categoryIds = $this->ask('Category IDs (comma separated, optional)');
        $categories = $categoryIds ? array_map('intval', explode(',', $categoryIds)) : [];

        $data = [
            'name' => $name,
            'description' => $description,
            'price' => $price,
            'image' => $image,
            'categories' => $categories,
        ];

        $result = $this->productService->createProduct($data);

        if (isset($result['errors'])) {
            $this->error('Validation failed:');
            foreach ($result['errors']->toArray() as $field => $messages) {
                $this->line($field . ': ' . implode(', ', $messages));
            }
            return 1;
        }

        $this->info('Product created successfully!');
        $this->line('ID: ' . $result->id);
        $this->line('Name: ' . $result->name);
        
        return 0;
    }
}
