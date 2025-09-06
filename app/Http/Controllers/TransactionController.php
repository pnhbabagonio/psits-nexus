<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Response;

class TransactionController extends Controller
{
    public function index()
    {
        // Get the authenticated user's transactions from the database
        $transactions = Transaction::where('user_id', Auth::id())
            ->latest() // Order by most recent first
            ->get()
            ->map(function ($tx) {
                return [
                    'id' => $tx->id,
                    'date' => $tx->created_at->format('Y-m-d'),
                    'type' => $tx->type,
                    'description' => $tx->description,
                    'amount' => $tx->amount,
                    'status' => $tx->status,
                ];
            });

        return Inertia::render('TransactionHistory', [
            'transactions' => $transactions,
        ]);
    }

    public function exportCsv()
    {
        $transactions = Transaction::where('user_id', Auth::id())->get();

        $csv = "Date,Type,Description,Amount,Status\n";
        foreach ($transactions as $tx) {
            $csv .= "{$tx->created_at->format('Y-m-d')},{$tx->type},{$tx->description},{$tx->amount},{$tx->status}\n";
        }

        return Response::make($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="transactions.csv"',
        ]);
    }

    public function exportPdf()
    {
        $transactions = Transaction::where('user_id', Auth::id())->get();

        $pdf = Pdf::loadView('receipts.transactions_all', compact('transactions'));

        return $pdf->download('transactions.pdf');
    }


    // ✅ Secure receipt download
    public function receipt($id)
    {
        $transaction = Transaction::findOrFail($id);

        // Mandatory ownership check for security
        if ($transaction->user_id !== Auth::id()) {
            abort(403);
        }

        $pdf = Pdf::loadView('transactions.receipt', compact('transaction'));
        // Stream PDF to browser instead of forcing download
        return $pdf->stream("receipt-{$transaction->id}.pdf");
    }
}
