<?php

use App\Http\Controllers\ClothingCategoryController;
use App\Http\Controllers\ClothingItemController;
use Inertia\Inertia;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => Inertia::render('Dashboard'))->name('dashboard');
Route::resource('clothing-categories', ClothingCategoryController::class)
    ->only(['index', 'store', 'update', 'destroy']);
Route::resource('clothing', ClothingItemController::class)
    ->only(['index', 'store', 'update', 'destroy']);
