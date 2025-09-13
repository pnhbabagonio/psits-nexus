<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\PaymentType;
use App\Models\User;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = Payment::with(['user', 'paymentType']);
        
        if ($request->has('status')) {
            $query->where('payment_status', $request->status);
        }

        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->has('type')) {
            $query->where('payment_type_id', $request->type);
        }

        $payments = $query->orderBy('transaction_date', 'desc')->paginate(20);

        return response()->json($payments);
    }

    public function show($id)
    {
        $payment = Payment::with(['user', 'paymentType', 'eventRegistration'])->findOrFail($id);
        return response()->json($payment);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,user_id',
            'amount' => 'required|numeric|min:0.01',
            'payment_type_id' => 'required|exists:payment_types,payment_type_id',
            'payment_method' => 'required|string|max:50',
            'reference_number' => 'nullable|string|max:100|unique:payments,reference_number',
            'notes' => 'nullable|string',
            'receipt_url' => 'nullable|url'
        ]);

        $payment = Payment::create([
            ...$validated,
            'payment_status' => 'pending'
        ]);

        return response()->json($payment, 201);
    }

    public function update(Request $request, $id)
    {
        $payment = Payment::findOrFail($id);

        $validated = $request->validate([
            'amount' => 'sometimes|numeric|min:0.01',
            'payment_method' => 'sometimes|string|max:50',
            'notes' => 'nullable|string',
            'receipt_url' => 'nullable|url'
        ]);

        $payment->update($validated);

        return response()->json($payment);
    }

    public function updateStatus(Request $request, $id)
    {
        $payment = Payment::findOrFail($id);

        $validated = $request->validate([
            'payment_status' => 'required|in:pending,completed,failed,refunded',
            'processed_by' => 'required|exists:users,user_id',
            'notes' => 'nullable|string'
        ]);

        $payment->update($validated);

        return response()->json($payment);
    }

    public function destroy($id)
    {
        $payment = Payment::findOrFail($id);
        $payment->delete();

        return response()->json(['message' => 'Payment deleted successfully']);
    }
}