<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MyOrderController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\RoleMiddleware;
use Illuminate\Support\Facades\Route;

// Public menu with search and category buttons
Route::get('/', [HomeController::class, 'index'])->name('home');

// Guests only
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.attempt');
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register'])->name('register.attempt');
});

// Any logged-in user
Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ---- Customers: cart and their own orders ----
    Route::middleware(RoleMiddleware::class . ':customer')->group(function () {
        Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
        Route::post('/cart/add/{product}', [CartController::class, 'add'])->name('cart.add');
        Route::patch('/cart/{product}', [CartController::class, 'update'])->name('cart.update');
        Route::delete('/cart/{product}', [CartController::class, 'remove'])->name('cart.remove');
        Route::post('/checkout', [CartController::class, 'checkout'])->name('cart.checkout');

        Route::get('/my-orders', [MyOrderController::class, 'index'])->name('my-orders.index');
        Route::get('/my-orders/{order}', [MyOrderController::class, 'show'])->name('my-orders.show');
        Route::post('/my-orders/{order}/cancel', [MyOrderController::class, 'cancel'])->name('my-orders.cancel');
    });

    // ---- Staff (admin, manager, employee): orders ----
    Route::middleware(RoleMiddleware::class . ':admin,manager,employee')->group(function () {
        Route::resource('orders', OrderController::class)->except('destroy');
        Route::post('/orders/{order}/claim', [OrderController::class, 'claim'])->name('orders.claim');
    });

    // ---- Admin and manager: catalog, customers, deleting orders ----
    Route::middleware(RoleMiddleware::class . ':admin,manager')->group(function () {
        Route::delete('/orders/{order}', [OrderController::class, 'destroy'])->name('orders.destroy');
        Route::resource('categories', CategoryController::class);
        Route::resource('products', ProductController::class);
        Route::resource('customers', CustomerController::class);
        // Team: admin manages managers + employees, manager manages employees
        Route::resource('team', TeamController::class)->except('destroy')->parameters(['team' => 'member']);
    });

    // ---- Admin only: user accounts ----
    Route::middleware(RoleMiddleware::class . ':admin')->group(function () {
        Route::resource('users', UserController::class);
    });
});
