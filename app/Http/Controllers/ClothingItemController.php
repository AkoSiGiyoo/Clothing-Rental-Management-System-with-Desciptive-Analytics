<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClothingItemRequest;
use App\Http\Requests\UpdateClothingItemRequest;
use App\Models\ClothingCategory;
use App\Models\ClothingItem;
use App\Models\Inventory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class ClothingItemController extends Controller
{
    public function index(Request $request): Response
    {
        $search = $request->string('search')->trim()->toString();

        $clothingItems = ClothingItem::query()
            ->select([
                'id',
                'clothing_category_id',
                'name',
                'rental_price',
                'color',
                'size',
                'image_path',
                'status',
                'created_at',
            ])
            ->with([
                'category:id,name',
                'inventory:id,clothing_item_id,quantity',
            ])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('color', 'like', "%{$search}%")
                        ->orWhere('size', 'like', "%{$search}%")
                        ->orWhere('status', 'like', "%{$search}%")
                        ->orWhereHas('category', fn ($categoryQuery) => $categoryQuery->where('name', 'like', "%{$search}%"));
                });
            })
            ->latest()
            ->get()
            ->map(fn (ClothingItem $item) => [
                'id' => $item->id,
                'category_id' => $item->clothing_category_id,
                'category' => $item->category ? [
                    'id' => $item->category->id,
                    'name' => $item->category->name,
                ] : null,
                'category_name' => $item->category?->name,
                'name' => $item->name,
                'rental_price' => number_format((float) $item->rental_price, 2, '.', ''),
                'quantity' => $item->inventory?->quantity ?? 0,
                'color' => $item->color,
                'size' => $item->size,
                'image_path' => $item->image_path,
                'image_url' => $this->resolveImageUrl($item->image_path),
                'status' => $item->status,
                'created_at' => $item->created_at?->format('M d, Y'),
            ]);

        return Inertia::render('ManageClothing', [
            'categories' => ClothingCategory::query()
                ->orderBy('name')
                ->get(['id', 'name']),
            'clothingItems' => $clothingItems,
            'filters' => [
                'search' => $search,
            ],
            'stats' => [
                'total' => ClothingItem::query()->count(),
                'available' => ClothingItem::query()->where('status', 'available')->count(),
                'rented' => ClothingItem::query()->where('status', 'rented')->count(),
                'low_stock' => Inventory::query()->where('quantity', '<', 3)->count(),
            ],
        ]);
    }

    public function store(StoreClothingItemRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $imagePath = $request->file('image')?->store('clothing-items', 'public');

        DB::transaction(function () use ($validated, $imagePath) {
            $clothingItem = ClothingItem::create([
                'clothing_category_id' => $validated['clothing_category_id'],
                'name' => $validated['name'],
                'rental_price' => $validated['rental_price'],
                'color' => $validated['color'] ?? null,
                'size' => $validated['size'] ?? null,
                'image_path' => $imagePath,
                'status' => $validated['status'],
            ]);

            $clothingItem->inventory()->create([
                'quantity' => 0,
            ]);
        });

        return to_route('clothing.index');
    }

    public function update(UpdateClothingItemRequest $request, ClothingItem $clothing): RedirectResponse
    {
        $validated = $request->validated();
        $imagePath = $clothing->image_path;

        if ($request->boolean('remove_image') && $imagePath) {
            Storage::disk('public')->delete($imagePath);
            $imagePath = null;
        }

        if ($request->hasFile('image')) {
            if ($imagePath) {
                Storage::disk('public')->delete($imagePath);
            }

            $imagePath = $request->file('image')->store('clothing-items', 'public');
        }

        DB::transaction(function () use ($validated, $clothing, $imagePath) {
            $clothing->update([
                'clothing_category_id' => $validated['clothing_category_id'],
                'name' => $validated['name'],
                'rental_price' => $validated['rental_price'],
                'color' => $validated['color'] ?? null,
                'size' => $validated['size'] ?? null,
                'image_path' => $imagePath,
                'status' => $validated['status'],
            ]);

            $clothing->inventory()->firstOrCreate(
                ['clothing_item_id' => $clothing->id],
                ['quantity' => 0],
            );
        });

        return to_route('clothing.index');
    }

    public function destroy(ClothingItem $clothing): RedirectResponse
    {
        if ($clothing->image_path) {
            Storage::disk('public')->delete($clothing->image_path);
        }

        $clothing->delete();

        return to_route('clothing.index');
    }

    private function resolveImageUrl(?string $imagePath): ?string
    {
        if (! $imagePath) {
            return null;
        }

        $normalizedPath = str_replace('\\', '/', $imagePath);
        $baseUrl = rtrim(request()->getBaseUrl(), '/');
        $storagePrefix = ($baseUrl !== '' ? $baseUrl : '').'/storage/';

        if (str_starts_with($normalizedPath, 'http://') || str_starts_with($normalizedPath, 'https://')) {
            return $normalizedPath;
        }

        if (str_starts_with($normalizedPath, '/storage/')) {
            return ($baseUrl !== '' ? $baseUrl : '').$normalizedPath;
        }

        if (str_starts_with($normalizedPath, 'storage/')) {
            return $storagePrefix.substr($normalizedPath, 8);
        }

        if (str_starts_with($normalizedPath, 'public/')) {
            $normalizedPath = substr($normalizedPath, 7);
        }

        return $storagePrefix.ltrim($normalizedPath, '/');
    }
}
