<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Transaction Receipt</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
            background-color: #f7f7f7;
        }
        .receipt-container {
            max-width: 600px;
            margin: auto;
            background: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0px 4px 10px rgba(0,0,0,0.1);
        }
        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }
        .receipt-info {
            margin-bottom: 20px;
        }
        .receipt-info p {
            margin: 5px 0;
            font-size: 16px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table th, table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        table th {
            background-color: #f2f2f2;
        }
        .total {
            text-align: right;
            font-size: 18px;
            font-weight: bold;
            margin-top: 10px;
        }
        .footer {
            text-align: center;
            color: #777;
            font-size: 14px;
            margin-top: 30px;
        }
    </style>
</head>
<body>
    <div class="receipt-container">
        <h1>Transaction Receipt</h1>

        <div class="receipt-info">
            <p><strong>Date:</strong> {{ $transaction->created_at->format('Y-m-d') }}</p>
            <p><strong>Category:</strong> {{ ucfirst($transaction->type) }}</p>
            <p><strong>Description:</strong> {{ $transaction->description }}</p>
            <p><strong>Status:</strong> {{ ucfirst($transaction->status) }}</p>
            <p><strong>Owner:</strong> {{ $transaction->user->name ?? 'Unknown' }}</p>
        </div>

        <table>
            <tr>
                <th>Amount</th>
            </tr>
            <tr>
                <td>Php {{ number_format($transaction->amount, 2) }}</td>
            </tr>
        </table>

        <div class="footer">
            Thank you for your transaction!
        </div>
    </div>
</body>
</html>
