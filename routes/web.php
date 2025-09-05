<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

use App\Http\Controllers\UserController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\PaymentController;

// --------------------
// Public Routes
// --------------------
Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

// --------------------
// Authenticated & Verified Routes (Core App)
// --------------------
Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    // --------------------
    // Financial Module
    // --------------------
    Route::get('/financial-reports', function () {
        return Inertia::render('FinancialReports');
    })->name('financial-reports');

    Route::get('/expenses-tracking', function () {
        return Inertia::render('ExpenseTracking');
    })->name('expenses-tracking');

    // --------------------
    // Platform Guide
    // --------------------
    Route::get('/platform-guide', function () {
        return Inertia::render('PlatformGuide');
    })->name('platform-guide');

    // --------------------
    // Payments Module
    // --------------------
    Route::prefix('payments')->name('payments.')->group(function () {
        Route::get('/qr-generator', function () {
            return Inertia::render('Payments/QrGenerator');
        })->name('qr-generator');

        Route::get('/record', function () {
            return Inertia::render('Payments/RecordPayment');
        })->name('record');

        Route::post('/record', [PaymentController::class, 'record'])
            ->name('record.store');
    });

    // --------------------
    // User Management Module
    // --------------------
    Route::get('/user-management', [UserController::class, 'index'])->name('user-management');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

    // --------------------
    // Event Management Module
    // --------------------
    Route::get('/events', [EventController::class, 'index'])->name('events.index');
    Route::get('/events/{id}', [EventController::class, 'show'])->name('events.show');
    Route::get('/event-attendees', [EventController::class, 'attendees'])->name('events.attendees');
    Route::get('/event-analysis', [EventController::class, 'analysis'])->name('events.analysis');

    // --------------------
    // Transactions Module
    // --------------------
    Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');
    Route::get('/transactions/export/csv', [TransactionController::class, 'exportCsv'])->name('transactions.export.csv');
    Route::get('/transactions/export/pdf', [TransactionController::class, 'exportPdf'])->name('transactions.export.pdf');
    Route::get('/transactions/{id}/receipt', [TransactionController::class, 'receipt'])->name('transactions.receipt');
});

// --------------------
// Chat Module (Auth Only, Verification Optional)
// --------------------
Route::middleware('auth')->group(function () {
    Route::get('/help-support', [ChatController::class, 'index'])->name('chat.index');
    Route::post('/help-support', [ChatController::class, 'store'])->name('chat.store');
    Route::delete('/help-support/clear', [ChatController::class, 'clear'])->name('chat.clear');
});

// --------------------
// Include Authentication & Settings Routes
// --------------------
require __DIR__ . '/auth.php';
require __DIR__ . '/settings.php';
