<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\CartController;

// Home
Route::get('/', [HomeController::class, 'home'])->name('home');
Route::redirect('/home', '/')->name('home.redirect');

// WITH USERNAME
Route::get('/u/{username}', [HomeController::class, 'homeForUser'])
    ->middleware('auth.user')
    ->name('home.user');

// Product detail page
Route::get('/products/{id}', [HomeController::class, 'productDetail'])
    ->name('products.show');

// Cart page
Route::get('/cart', [CartController::class, 'index'])->name('cart');

// Cart actions
Route::post('/cart/add/{product}', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/items/{item}/update', [CartController::class, 'update'])->name('cart.item.update');

// Account page
Route::view('/account', 'account')->name('account');

// Admin
Route::middleware('admin')->group(function () {

    // Admin Page (username-based)
    Route::get('/a/{username}', [AdminController::class, 'indexForUser'])
        ->name('admin.user');

    // Product CRUD
    Route::post('/admin/products', [AdminController::class, 'storeProduct'])->name('admin.products.store');
    Route::get('/admin/products/{product}/edit', [AdminController::class, 'editProduct'])->name('admin.products.edit');
    Route::put('/admin/products/{product}', [AdminController::class, 'updateProduct'])->name('admin.products.update');
    Route::delete('/admin/products/{product}', [AdminController::class, 'destroyProduct'])->name('admin.products.destroy');

    // Category CRUD
    Route::post('/admin/categories', [AdminController::class, 'storeCategory'])->name('admin.categories.store');
    Route::get('/admin/categories/{category}/edit', [AdminController::class, 'editCategory'])->name('admin.categories.edit');
    Route::put('/admin/categories/{category}', [AdminController::class, 'updateCategory'])->name('admin.categories.update');
    Route::delete('/admin/categories/{category}', [AdminController::class, 'destroyCategory'])->name('admin.categories.destroy');
});

// LOGIN
Route::get('/login',  [LoginController::class, 'show'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.submit');

// REGISTER
Route::get('/register',  [LoginController::class, 'showRegister'])->name('register');
Route::post('/register', [LoginController::class, 'register'])->name('register.submit');

// LOGOUT
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');