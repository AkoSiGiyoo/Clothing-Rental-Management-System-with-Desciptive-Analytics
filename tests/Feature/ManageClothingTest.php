<?php

namespace Tests\Feature;

use App\Models\ClothingCategory;
use App\Models\ClothingItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ManageClothingTest extends TestCase
{
    use RefreshDatabase;

    public function test_manage_clothing_page_is_accessible(): void
    {
        $response = $this->get('/clothing');

        $response->assertOk();
    }

    public function test_clothing_item_can_be_created_with_inventory(): void
    {
        Storage::fake('public');

        $category = ClothingCategory::create([
            'name' => 'Formal Wear',
        ]);

        $response = $this->post('/clothing', [
            'clothing_category_id' => $category->id,
            'name' => 'Emerald Gown',
            'rental_price' => 1500,
            'color' => 'Green',
            'size' => 'M',
            'image' => UploadedFile::fake()->create('gown.jpg', 100, 'image/jpeg'),
            'status' => 'available',
        ]);

        $response->assertRedirect('/clothing');

        $this->assertDatabaseHas('clothing_items', [
            'name' => 'Emerald Gown',
            'clothing_category_id' => $category->id,
            'status' => 'available',
        ]);

        $this->assertDatabaseHas('inventories', [
            'quantity' => 0,
        ]);

        $item = ClothingItem::query()->first();

        $this->assertNotNull($item?->image_path);
        Storage::disk('public')->assertExists($item->image_path);
    }

    public function test_clothing_item_can_be_updated(): void
    {
        Storage::fake('public');

        $firstCategory = ClothingCategory::create(['name' => 'Gowns']);
        $secondCategory = ClothingCategory::create(['name' => 'Barong']);

        $item = ClothingItem::create([
            'clothing_category_id' => $firstCategory->id,
            'name' => 'Classic Gown',
            'rental_price' => 900,
            'color' => 'White',
            'size' => 'S',
            'image_path' => UploadedFile::fake()->create('classic-gown.jpg', 100, 'image/jpeg')->store('clothing-items', 'public'),
            'status' => 'available',
        ]);

        $item->inventory()->create(['quantity' => 2]);

        $response = $this->post("/clothing/{$item->id}", [
            '_method' => 'put',
            'clothing_category_id' => $secondCategory->id,
            'name' => 'Modern Barong',
            'rental_price' => 1200,
            'color' => 'Ivory',
            'size' => 'L',
            'image' => UploadedFile::fake()->create('barong.jpg', 100, 'image/jpeg'),
            'status' => 'reserved',
        ]);

        $response->assertRedirect('/clothing');

        $this->assertDatabaseHas('clothing_items', [
            'id' => $item->id,
            'clothing_category_id' => $secondCategory->id,
            'name' => 'Modern Barong',
            'status' => 'reserved',
        ]);

        $this->assertDatabaseHas('inventories', [
            'clothing_item_id' => $item->id,
            'quantity' => 2,
        ]);

        $item->refresh();
        $this->assertNotNull($item->image_path);
        Storage::disk('public')->assertExists($item->image_path);
    }

    public function test_clothing_item_can_be_deleted(): void
    {
        Storage::fake('public');

        $category = ClothingCategory::create(['name' => 'Traditional']);

        $item = ClothingItem::create([
            'clothing_category_id' => $category->id,
            'name' => 'Baro at Saya',
            'rental_price' => 1000,
            'color' => null,
            'size' => null,
            'image_path' => UploadedFile::fake()->create('baro.jpg', 100, 'image/jpeg')->store('clothing-items', 'public'),
            'status' => 'maintenance',
        ]);

        $item->inventory()->create(['quantity' => 1]);

        $response = $this->delete("/clothing/{$item->id}");

        $response->assertRedirect('/clothing');
        $this->assertDatabaseMissing('clothing_items', ['id' => $item->id]);
        $this->assertDatabaseMissing('inventories', ['clothing_item_id' => $item->id]);
        Storage::disk('public')->assertMissing($item->image_path);
    }

    public function test_clothing_category_can_be_created_from_manage_clothing_flow(): void
    {
        $response = $this->post('/clothing-categories', [
            'name' => 'Cultural Wear',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('clothing_categories', [
            'name' => 'Cultural Wear',
        ]);
    }
}
