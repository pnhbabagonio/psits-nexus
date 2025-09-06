<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index()
    {
        // Admin sees all, member sees only their own
        if (Auth::user()->role === 'Admin') {
            $transactions = Transaction::with('user')->latest()->get();
        } else {
            $transactions = Transaction::with('user')
                ->where('user_id', Auth::id())
                ->latest()
                ->get();
        }

        $transactions = $transactions->map(function ($tx) {
            return [
                'id'          => $tx->id,
                'owner'       => $tx->user->name ?? 'Unknown',
                'date'        => $tx->created_at->format('Y-m-d'),
                'type'        => $tx->type,
                'description' => $tx->description,
                'amount'      => $tx->amount,
                'status'      => $tx->status,
            ];
        });

        return Inertia::render('TransactionHistory', [
            'transactions' => $transactions,
            'is_admin' => Auth::user()->role === 'Admin',
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id'     => 'required|exists:users,id', // the owner of the transaction
            'type'        => 'required|string',
            'description' => 'required|string',
            'amount'      => 'required|numeric',
            'status'      => 'required|string',
        ]);

        Transaction::create([
            'user_id'     => $validated['user_id'],
            'type'        => $validated['type'],
            'description' => $validated['description'],
            'amount'      => $validated['amount'],
            'status'      => $validated['status'],
        ]);

        return redirect()->route('transactions.index')
            ->with('success', 'Transaction recorded successfully.');
    }

    public function exportCsv()
    {
        if (Auth::user()->role === 'Admin') {
            $transactions = Transaction::with('user')->latest()->get();
        } else {
            $transactions = Transaction::with('user')
                ->where('user_id', Auth::id())
                ->latest()
                ->get();
        }

        $csv = "Date,Type,Description,Amount,Status,Owner\n";

        foreach ($transactions as $tx) {
            $csv .= "{$tx->created_at->format('Y-m-d')},{$tx->type},{$tx->description},{$tx->amount},{$tx->status},"
                . ($tx->user->name ?? 'Unknown') . "\n";
        }

        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename=transactions.csv');
    }

    public function exportPdf()
    {
        if (Auth::user()->role === 'Admin') {
            $transactions = Transaction::with('user')->latest()->get();
        } else {
            $transactions = Transaction::with('user')
                ->where('user_id', Auth::id())
                ->latest()
                ->get();
        }

        $pdf = Pdf::loadView('receipts.transactions_all', [
            'transactions' => $transactions,
            'isAdmin' => Auth::user()->role === 'Admin',
        ]);

        return $pdf->download('transactions.pdf');
    }

    public function receipt($id)
    {
        $transaction = Transaction::with('user')->findOrFail($id);

        // Security: members only see their own receipts
        if (Auth::user()->role !== 'Admin' && $transaction->user_id !== Auth::id()) {
            abort(403, 'Access restricted. Administrative access only.');
        }

        $pdf = Pdf::loadView('transactions.receipt', compact('transaction'));
        return $pdf->stream("receipt-{$transaction->id}.pdf");
    }
}
