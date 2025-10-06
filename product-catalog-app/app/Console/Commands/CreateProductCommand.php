<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CreateProductCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'product:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new product via CLI';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
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
            'categories' => $categories
        ];

        $service = app(\App\Services\ProductService::class);
        $result = $service->createProduct($data);

        if (isset($result['errors'])) {
            $this->error('Validation failed:');
            foreach ($result['errors']->toArray() as $field => $messages) {
                $this->line($field . ': ' . implode(', ', $messages));
            }
            return 1;
        }

        if (!empty($categories)) {
            $result->categories()->sync($categories);
        }

        $this->info('Product created successfully!');
        $this->line('ID: ' . $result->id);
        $this->line('Name: ' . $result->name);
        return 0;
    }
}
