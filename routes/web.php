<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\TransactionController;
use Inertia\Inertia;

// Public Routes
Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

// Authenticated & Verified Routes (Core Application)
Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard
    Route::get('dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    // Financial Module
    Route::get('financial-reports', function () {
        return Inertia::render('FinancialReports');
    })->name('financial-reports');

    Route::get('expenses-tracking', function () {
        return Inertia::render('ExpenseTracking');
    })->name('expenses-tracking');

    // Platform Guide
    Route::get('platform-guide', function () {
        return Inertia::render('PlatformGuide');
    })->name('platform-guide');

    // Payments
    Route::get('/payments/qr-generator', function () {
        return Inertia::render('Payments/QrGenerator');
    })->name('payments.qr-generator');

    // User Management Module
    Route::get('user-management', [UserController::class, 'index'])->name('user-management');
    Route::post('users', [UserController::class, 'store'])->name('users.store');
    Route::put('users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

    // Event Management Module
    Route::get('events', [EventController::class, 'index'])->name('events.index');
    Route::get('events/{id}', [EventController::class, 'show'])->name('events.show');
    Route::get('event-attendees', [EventController::class, 'attendees'])->name('events.attendees');
    Route::get('event-analysis', [EventController::class, 'analysis'])->name('events.analysis');

    // Transactions Module
    // FIXED: The controller now handles rendering the page AND the data logic.
    Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');

    // Transaction Exports
    Route::get('/transactions/export/csv', [TransactionController::class, 'exportCsv'])->name('transactions.export.csv');
    Route::get('/transactions/export/pdf', [TransactionController::class, 'exportPdf'])->name('transactions.export.pdf');

    // Receipt download
    Route::get('/transactions/{id}/receipt', [TransactionController::class, 'receipt'])
        ->name('transactions.receipt');
});

// Chat Routes (Auth required, but verification may not be)
Route::middleware('auth')->group(function () {
    Route::get('/help-support', [ChatController::class, 'index'])->name('chat.index');
    Route::post('/help-support', [ChatController::class, 'store'])->name('chat.store');
    Route::delete('/help-support/clear', [ChatController::class, 'clear'])->name('chat.clear');
});

// Include Authentication and Settings Routes
require __DIR__ . '/auth.php';
require __DIR__ . '/settings.php';
