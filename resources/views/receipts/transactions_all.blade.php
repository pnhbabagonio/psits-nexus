<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>All Transactions</title>
    <style>
        body { font-family: sans-serif; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; }
        th { background: #f4f4f4; }
    </style>
</head>
<body>
    <h2>All Transactions</h2>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Type</th>
                <th>Description</th>
                <th>Amount</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transactions as $tx)
                <tr>
                    <td>{{ $tx->created_at->format('Y-m-d') }}</td>
                    <td>{{ ucfirst($tx->type) }}</td>
                    <td>{{ $tx->description }}</td>
                    <td>₱{{ number_format($tx->amount, 2) }}</td>
                    <td>{{ $tx->status }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
