<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\ChatController;
use Inertia\Inertia;




Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('transactions', function () {
    return Inertia::render('TransactionHistory');
    //this should match resources/js/Pages/TransactionHistory.vue
})->middleware(['auth', 'verified'])->name('transactions');

Route::get('financial-reports', function () {
    return Inertia::render('FinancialReports');
})->middleware(['auth', 'verified'])->name('financial-reports');

Route::get('expenses-tracking', function () {
    return Inertia::render('ExpenseTracking');
})->middleware(['auth', 'verified'])->name('expenses-tracking');

// User management routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('user-management', [UserController::class, 'index'])->name('user-management');
    Route::post('users', [UserController::class, 'store'])->name('users.store');
    Route::put('users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
});

// Event Management routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('events', [EventController::class, 'index'])->name('events.index');
    Route::get('events/{id}', [EventController::class, 'show'])->name('events.show');
    Route::get('event-attendees', [EventController::class, 'attendees'])->name('events.attendees');
    Route::get('event-analysis', [EventController::class, 'analysis'])->name('events.analysis');
});


Route::middleware('auth')->group(function () {
    Route::get('/help-support', [ChatController::class, 'index'])->name('chat.index');
    Route::post('/help-support', [ChatController::class, 'store'])->name('chat.store');
    Route::delete('/help-support/clear', [ChatController::class, 'clear'])->name('chat.clear');
});


require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';

// Platform Guide page
Route::get('platform-guide', function () {
    return Inertia::render('PlatformGuide');
    // This should match resources/js/Pages/PlatformGuide.vue
})->middleware(['auth', 'verified'])->name('platform-guide');
