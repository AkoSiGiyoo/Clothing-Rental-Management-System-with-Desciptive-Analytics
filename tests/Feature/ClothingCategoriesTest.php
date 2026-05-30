<?php

namespace Tests\Feature;

use App\Models\ClothingCategory;
use App\Models\ClothingItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClothingCategoriesTest extends TestCase
{
    use RefreshDatabase;

    public function test_clothing_categories_page_is_accessible(): void
    {
        $response = $this->get('/clothing-categories');

        $response->assertOk();
    }

    public function test_clothing_category_can_be_created(): void
    {
        $response = $this->post('/clothing-categories', [
            'name' => 'Barong',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('clothing_categories', [
            'name' => 'Barong',
        ]);
    }

    public function test_clothing_category_can_be_updated(): void
    {
        $category = ClothingCategory::create([
            'name' => 'Filipinana',
        ]);

        $response = $this->put("/clothing-categories/{$category->id}", [
            'name' => 'Filipiniana',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('clothing_categories', [
            'id' => $category->id,
            'name' => 'Filipiniana',
        ]);
    }

    public function test_unused_clothing_category_can_be_deleted(): void
    {
        $category = ClothingCategory::create([
            'name' => 'Uniform',
        ]);

        $response = $this->delete("/clothing-categories/{$category->id}");

        $response->assertRedirect();
        $this->assertDatabaseMissing('clothing_categories', [
            'id' => $category->id,
        ]);
    }

    public function test_used_clothing_category_cannot_be_deleted(): void
    {
        $category = ClothingCategory::create([
            'name' => 'Gown',
        ]);

        ClothingItem::create([
            'clothing_category_id' => $category->id,
            'name' => 'Evening Gown',
            'rental_price' => 1500,
            'color' => 'Black',
            'size' => 'M',
            'image_path' => null,
            'brand' => 'Studio',
            'status' => 'available',
        ]);

        $response = $this->delete("/clothing-categories/{$category->id}");

        $response->assertRedirect();
        $this->assertDatabaseHas('clothing_categories', [
            'id' => $category->id,
        ]);
    }
}
