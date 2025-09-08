<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\AttendeeController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HelpSupportController;
use App\Http\Controllers\ChatbotController;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

// Replace the dashboard closure with controller
Route::get('dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Add API route for dashboard stats
Route::get('dashboard/stats', [DashboardController::class, 'getStats'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard.stats');

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
    Route::get('users/{user}', [UserController::class, 'show'])->name('users.show');
    Route::put('users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
});

// Event Management Routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('events', EventController::class);
});

// Attendee Management Routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/attendees', [AttendeeController::class, 'index'])->name('attendees');
    Route::post('/attendees', [AttendeeController::class, 'store'])->name('attendees.store');
    Route::get('/attendees/export', [AttendeeController::class, 'export'])->name('attendees.export');
});

// Event Analytics Routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics');
    Route::get('/analytics/export', [AnalyticsController::class, 'export'])->name('analytics.export');
});

// Help & Support routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('help-support', [HelpSupportController::class, 'index'])->name('help-support');
    Route::post('help-support', [HelpSupportController::class, 'store'])->name('help-support.store');
});

// ChatBot routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('ChatBot', [ChatbotController::class, 'index'])->name('ChatBot');
    Route::post('ChatBot', [ChatbotController::class, 'store'])->name('ChatBot.store');
});

// Platform Guide page
Route::get('platform-guide', function () {
    return Inertia::render('PlatformGuide');
    // This should match resources/js/Pages/PlatformGuide.vue
})->middleware(['auth', 'verified'])->name('platform-guide');

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
