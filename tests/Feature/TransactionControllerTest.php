<?php
// tests/Feature/TransactionControllerTest.php

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class); // Pest: Clears database between tests
// For PHPUnit: use RefreshDatabase; trait

beforeEach(function () {
    // This runs before each test
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

// Test the index page
it('displays transactions on index page', function () {
    $transactions = Transaction::factory()->count(5)->create([
        'user_id' => $this->user->id,
    ]);

    $response = $this->get(route('transactions.index'));

    $response->assertOk();
    $response->assertInertia(
        fn($page) => $page
            ->component('TransactionHistory')
            ->has('transactions', 5)
    );
});

// Test CSV Export
it('exports transactions as csv', function () {
    $transactions = Transaction::factory()->count(3)->create([
        'user_id' => $this->user->id,
    ]);

    $response = $this->get(route('transactions.export.csv'));

    $response->assertOk();
    $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
    $response->assertHeader(
        'Content-Disposition',
        'attachment; filename="transactions.csv"'
    );

    $csvContent = $response->getContent();
    $this->assertStringContainsString('Date,Type,Description,Amount,Status', $csvContent);

    foreach ($transactions as $transaction) {
        $this->assertStringContainsString($transaction->description, $csvContent);
    }
});

// Test PDF Export
it('exports transactions as pdf', function () {
    Transaction::factory()->count(2)->create([
        'user_id' => $this->user->id,
    ]);

    $response = $this->get(route('transactions.export.pdf'));

    $response->assertOk();
    $response->assertHeader('Content-Type', 'application/pdf');
    $response->assertHeader(
        'Content-Disposition',
        'attachment; filename=transactions.pdf'
    );
});

// Test Receipt Download - Success
it('allows user to download their own receipt', function () {
    $myTransaction = Transaction::factory()->create([
        'user_id' => $this->user->id,
    ]);

    $response = $this->get(route('transactions.receipt', $myTransaction->id));

    $response->assertOk();
    $response->assertHeader('Content-Type', 'application/pdf');
    $response->assertHeader(
        'Content-Disposition',
        'attachment; filename=receipt-' . $myTransaction->id . '.pdf'
    );
});

// Test Receipt Download - Authorization Failure
it('forbids user from downloading another users receipt', function () {
    $otherUser = User::factory()->create();
    $otherUserTransaction = Transaction::factory()->create([
        'user_id' => $otherUser->id,
    ]);

    $response = $this->get(route('transactions.receipt', $otherUserTransaction->id));
    $response->assertForbidden();
});

// Test Receipt Download - Not Found
it('returns 404 for non-existent receipt', function () {
    $response = $this->get(route('transactions.receipt', 99999));
    $response->assertNotFound();
});
