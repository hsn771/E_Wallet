<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\LiabilityController;

// Guest Routes
Route::middleware('guest')->group(function () {
    Route::get('/', [AuthController::class, 'showLogin']); // Keep root URL working
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::post('/', [AuthController::class, 'login'])->name('login.post');
    
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
});

// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::post('/password', [AuthController::class, 'updatePassword'])->name('password.update');
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Wallets
    Route::resource('wallets', WalletController::class);
    Route::post('/wallets/{wallet}/add-balance', [WalletController::class, 'addBalance'])->name('wallets.addBalance');
    
    // Transactions
    Route::resource('transactions', TransactionController::class);
    
    // Categories
    Route::resource('categories', CategoryController::class);
    
    // Assets
    Route::resource('assets', AssetController::class);
    
    // Liabilities
    Route::resource('liabilities', LiabilityController::class);
});
