<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\AttendeeController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HelpSupportController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ReportController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

/*
|--------------------------------------------------------------------------
| Dashboard
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/stats', [DashboardController::class, 'getStats'])->name('dashboard.stats');
});

/*
|--------------------------------------------------------------------------
| User Management
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/user-management', [UserController::class, 'index'])->name('user-management');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
});

/*
|--------------------------------------------------------------------------
| Financial Management
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {
    // Transactions
    Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');
    Route::get('/transactions/export/csv', [TransactionController::class, 'exportCsv'])->name('transactions.export.csv');
    Route::get('/transactions/export/pdf', [TransactionController::class, 'exportPdf'])->name('transactions.export.pdf');
    Route::get('/transactions/{id}/receipt', [TransactionController::class, 'receipt'])->name('transactions.receipt');

    // Financial Reports
    Route::get('/financial-reports', fn() => Inertia::render('FinancialReports'))->name('financial-reports');

    // Expenses Tracking
    Route::get('/expenses-tracking', fn() => Inertia::render('ExpenseTracking'))->name('expenses-tracking');
});

/*
|--------------------------------------------------------------------------
| Event Management
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {
    // Main Event Management Page with tabs
    Route::get('/event-management', fn() => Inertia::render('EventManagement'))->name('event-management');
    
    // Keep your API routes for the individual tab components
    Route::resource('events', EventController::class);
    Route::get('/attendees', [AttendeeController::class, 'index'])->name('attendees');
    Route::post('/attendees', [AttendeeController::class, 'store'])->name('attendees.store');
    Route::get('/attendees/export', [AttendeeController::class, 'export'])->name('attendees.export');
    Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics');
});

/*
|--------------------------------------------------------------------------
| Payment
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/payment', fn() => Inertia::render('Payment'))->name('payment.index');

    Route::prefix('payments')->name('payments.')->group(function () {
        Route::get('/qr-generator', fn() => Inertia::render('QrGenerator'))->name('qr-generator');
        Route::get('/record', fn() => Inertia::render('RecordPayment'))->name('record');
        Route::post('/record', [PaymentController::class, 'record'])->name('record.store');
    });
});

/*
|--------------------------------------------------------------------------
| Reports
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/reports', fn() => Inertia::render('Reports'))->name('reports');
    Route::get('/reports/stats', [ReportController::class, 'index'])->name('reports.stats');
});

/*
|--------------------------------------------------------------------------
| Help & Support
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/help-support', [HelpSupportController::class, 'index'])->name('help-support');
    Route::post('/help-support', [HelpSupportController::class, 'store'])->name('help-support.store');
});

/*
|--------------------------------------------------------------------------
| ChatBot
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/ChatBot', [ChatbotController::class, 'index'])->name('ChatBot');
    Route::post('/ChatBot', [ChatbotController::class, 'store'])->name('ChatBot.store');
});

/*
|--------------------------------------------------------------------------
| Platform Guide
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/platform-guide', fn() => Inertia::render('PlatformGuide'))->name('platform-guide');
});

/*
|--------------------------------------------------------------------------
| Registration Pending
|--------------------------------------------------------------------------
*/
Route::get('/registration-pending', fn() => Inertia::render('auth/RegistrationPending'))->name('registration.pending');

/*
|--------------------------------------------------------------------------
| Extra Files
|--------------------------------------------------------------------------
*/
require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
