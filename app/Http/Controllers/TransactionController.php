<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class TransactionController extends Controller
{
    public function index()
    {
        $transactions = Transaction::where('user_id', Auth::id())
            ->latest()
            ->get()
            ->map(function ($tx) {
                return [
                    'date' => $tx->created_at->format('Y-m-d'),
                    'type' => $tx->type,
                    'description' => $tx->description,
                    'amount' => $tx->amount,
                    'status' => $tx->status,
                ];
            });

        return Inertia::render('TransactionHistory', [ // 👈 matches your Vue file name
            'transactions' => $transactions,
        ]);
    }
}
