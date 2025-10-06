<?php
namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Category;
use App\Models\Product;

class ProductCreationTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_can_be_created_via_api()
    {
        $category = Category::create(['name' => 'Test Category']);
        $response = $this->postJson('/api/products', [
            'name' => 'Test Product',
            'description' => 'Test Description',
            'price' => 99.99,
            'image' => 'test.jpg',
            'categories' => [$category->id]
        ]);
        $response->assertStatus(201);
        $this->assertDatabaseHas('products', ['name' => 'Test Product']);
        $this->assertDatabaseHas('category_product', ['category_id' => $category->id]);
    }

    public function test_product_creation_validation()
    {
        $response = $this->postJson('/api/products', [
            'name' => '',
            'description' => '',
            'price' => '',
        ]);
        $response->assertStatus(422);
        $response->assertJsonStructure(['errors']);
    }
}
