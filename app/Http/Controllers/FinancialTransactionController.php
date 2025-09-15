<?php

namespace App\Http\Controllers;

use App\Models\FinancialTransaction;
use App\Models\TransactionCategory;
use Illuminate\Http\Request;

class FinancialTransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = FinancialTransaction::with(['category', 'recorder', 'approver', 'event', 'payment']);
        
        if ($request->has('type')) {
            $query->where('transaction_type', $request->type);
        }

        if ($request->has('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->has('status')) {
            $query->where('approval_status', $request->status);
        }

        if ($request->has('start_date')) {
            $query->where('transaction_date', '>=', $request->start_date);
        }

        if ($request->has('end_date')) {
            $query->where('transaction_date', '<=', $request->end_date);
        }

        $transactions = $query->orderBy('transaction_date', 'desc')->paginate(20);

        return response()->json($transactions);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'transaction_type' => 'required|in:income,expense,transfer',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'required|string',
            'category_id' => 'required|exists:transaction_categories,category_id',
            'payment_id' => 'nullable|exists:payments,payment_id',
            'event_id' => 'nullable|exists:events,event_id',
            'receipt_image' => 'nullable|url',
            'notes' => 'nullable|string'
        ]);

        $transaction = FinancialTransaction::create([
            ...$validated,
            'recorded_by' => auth()->id(),
            'approval_status' => 'pending'
        ]);

        return response()->json($transaction, 201);
    }

    public function updateApproval(Request $request, $id)
    {
        $transaction = FinancialTransaction::findOrFail($id);

        $validated = $request->validate([
            'approval_status' => 'required|in:pending,approved,rejected',
            'notes' => 'nullable|string'
        ]);

        $updateData = [
            'approval_status' => $validated['approval_status'],
            'notes' => $validated['notes'] ?? $transaction->notes
        ];

        if ($validated['approval_status'] === 'approved') {
            $updateData['approved_by'] = auth()->id();
            $updateData['approval_date'] = now();
        }

        $transaction->update($updateData);

        return response()->json($transaction);
    }

    public function destroy($id)
    {
        $transaction = FinancialTransaction::findOrFail($id);
        $transaction->delete();

        return response()->json(['message' => 'Transaction deleted successfully']);
    }
}