<?php

use Illuminate\Support\Facades\Route;
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

Route::get('financial-reports', function(){
    return Inertia::render('FinancialReports');
})->middleware(['auth', 'verified'])->name('financial-reports');

Route::get('expenses-tracking', function(){
    return Inertia::render('ExpenseTracking');
})->middleware(['auth', 'verified'])->name('expenses-tracking');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
