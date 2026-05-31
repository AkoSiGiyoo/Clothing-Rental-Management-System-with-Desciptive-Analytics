<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClothingCategoryRequest;
use App\Http\Requests\UpdateClothingCategoryRequest;
use App\Models\ClothingCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ClothingCategoryController extends Controller
{
    public function index(Request $request): Response
    {
        // Search term is returned to the page so the filter input stays in sync.
        $search = $request->string('search')->trim()->toString();

        $categories = ClothingCategory::query()
            ->select(['id', 'name', 'created_at'])
            ->withCount('clothingItems')
            ->when($search !== '', fn ($query) => $query->where('name', 'like', "%{$search}%"))
            ->orderBy('name')
            ->get()
            ->map(fn (ClothingCategory $category) => [
                'id' => $category->id,
                'name' => $category->name,
                'clothing_items_count' => $category->clothing_items_count,
                'created_at' => $category->created_at?->format('M d, Y'),
            ]);

        return Inertia::render('ClothingCategories', [
            'categories' => $categories,
            'filters' => [
                'search' => $search,
            ],
            'stats' => [
                'total' => ClothingCategory::query()->count(),
                'used' => ClothingCategory::query()->has('clothingItems')->count(),
                'unused' => ClothingCategory::query()->doesntHave('clothingItems')->count(),
            ],
        ]);
    }

    public function store(StoreClothingCategoryRequest $request): RedirectResponse
    {
        // Validation is handled by FormRequest; controller focuses on persistence.
        ClothingCategory::create($request->validated());

        return back();
    }

    public function update(UpdateClothingCategoryRequest $request, ClothingCategory $clothingCategory): RedirectResponse
    {
        $clothingCategory->update($request->validated());

        return back();
    }

    public function destroy(ClothingCategory $clothingCategory): RedirectResponse
    {
        // Guard deletion when still referenced by clothing items.
        if ($clothingCategory->clothingItems()->exists()) {
            return back();
        }

        $clothingCategory->delete();

        return back();
    }
}
