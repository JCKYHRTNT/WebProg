<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\AccountController;

// Guest Home
Route::get('/', [HomeController::class, 'home'])->name('home');
Route::redirect('/home', '/')->name('home.redirect');

// User Home
Route::get('/u/{username}', [HomeController::class, 'homeForUser'])
    ->middleware('auth.user')
    ->name('home.user');

// Guest Product Detail (user/guest only)
Route::get('/products/{id}', [HomeController::class, 'productDetail'])
    ->name('products.show');

// User Product Detail
Route::get('/u/{username}/products/{id}', [HomeController::class, 'productDetail'])
    ->middleware('auth.user')
    ->name('products.show.user');

// Guest cart
Route::get('/cart', fn () => redirect()->route('login'))
    ->name('cart.redirect');

// User cart
Route::get('/u/{username}/cart', [CartController::class, 'index'])
    ->middleware('auth.user')
    ->name('cart');

// Cart
Route::post('/u/{username}/cart/add/{product}', [CartController::class, 'add'])
    ->middleware('auth.user')
    ->name('cart.add');

Route::post('/u/{username}/cart/items/{item}/update', [CartController::class, 'update'])
    ->middleware('auth.user')
    ->name('cart.item.update');

// Account Page
// Guest account
Route::get('/account', fn () => redirect()->route('login'))
    ->name('account.redirect');

// User account (view)
Route::get('/u/{username}/account', [AccountController::class, 'userAccount'])
    ->middleware('auth.user')
    ->name('account');

// Admin account (view)
Route::get('/a/{username}/account', [AccountController::class, 'adminAccount'])
    ->middleware('admin')
    ->name('account.admin');

// Account update/delete (user)
Route::post('/u/{username}/account/update', [AccountController::class, 'update'])
    ->middleware('auth.user')
    ->name('account.update');

Route::post('/u/{username}/account/delete', [AccountController::class, 'destroy'])
    ->middleware('auth.user')
    ->name('account.delete');

// Account update/delete (admin)
Route::post('/a/{username}/account/update', [AccountController::class, 'update'])
    ->middleware('admin')
    ->name('account.admin.update');

Route::post('/a/{username}/account/delete', [AccountController::class, 'destroy'])
    ->middleware('admin')
    ->name('account.admin.delete');

// Admin
Route::middleware('admin')->group(function () {

    // Admin Home
    Route::get('/a/{username}', [AdminController::class, 'indexForUser'])
        ->name('admin.user');

    // Admin CRUD
    Route::get('/a/{username}/admin', [AdminController::class, 'crud'])
        ->name('admin.crud');

    // Promote admin
    Route::post('/a/{username}/admin/promote', [AdminController::class, 'promoteAdmin'])
        ->name('admin.crud.promote');

    // Demote admin
    Route::post('/a/{username}/admin/demote', [AdminController::class, 'demoteAdmin'])
        ->name('admin.crud.demote');

    // ADMIN PRODUCT DETAIL
    Route::get('/a/{username}/products/{product}', [AdminController::class, 'productDetail'])
        ->name('admin.products.show');

    // PRODUCT CRUD
    Route::post('/a/{username}/products', [AdminController::class, 'storeProduct'])
        ->name('admin.products.store');

    Route::get('/a/{username}/products/{product}/edit', [AdminController::class, 'editProduct'])
        ->name('admin.products.edit');

    Route::put('/a/{username}/products/{product}', [AdminController::class, 'updateProduct'])
        ->name('admin.products.update');

    Route::delete('/a/{username}/products/{product}', [AdminController::class, 'destroyProduct'])
        ->name('admin.products.destroy');

    // CATEGORY CRUD
    Route::post('/a/{username}/categories', [AdminController::class, 'storeCategory'])
        ->name('admin.categories.store');

    Route::get('/a/{username}/categories/{category}/edit', [AdminController::class, 'editCategory'])
        ->name('admin.categories.edit');

    Route::put('/a/{username}/categories/{category}', [AdminController::class, 'updateCategory'])
        ->name('admin.categories.update');

    Route::delete('/a/{username}/categories/{category}', [AdminController::class, 'destroyCategory'])
        ->name('admin.categories.destroy');
});

// Authentication
Route::get('/login',  [LoginController::class, 'show'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.submit');

Route::get('/register',  [LoginController::class, 'showRegister'])->name('register');
Route::post('/register', [LoginController::class, 'register'])->name('register.submit');

Route::get('/logout', [LoginController::class, 'logout'])->name('logout');