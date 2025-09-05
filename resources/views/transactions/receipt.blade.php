<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Transaction Receipt</title>
</head>
<body>
    <h1>Transaction Receipt</h1>
    <p>Date: {{ $transaction->created_at->format('Y-m-d') }}</p>
    <p>Type: {{ ucfirst($transaction->type) }}</p>
    <p>Description: {{ $transaction->description }}</p>
    <p>Amount: ₱{{ number_format($transaction->amount, 2) }}</p>
    <p>Status: {{ $transaction->status }}</p>
</body>
</html>
