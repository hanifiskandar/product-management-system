<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/products')->name('home');

Route::resource('products', ProductController::class)->except(['show']);
Route::resource('categories', CategoryController::class)->only(['index', 'store', 'update']);
