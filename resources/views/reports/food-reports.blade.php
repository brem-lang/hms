<!DOCTYPE html>
<html>

<head>
    <title>Sales Report</title>
    <style>
        body {
            font-family: sans-serif;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 8px 10px;
            border: 1px solid #ccc;
        }

        th {
            background: #eee;
        }
    </style>
</head>

<body>
    <h2>Sales Report</h2>
    <table>
        <thead>
            <tr>
                <th>Food Name</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Amount to Pay</th>
            </tr>
        </thead>
        <tbody>
            @php $totalAmount = 0; @endphp
            @foreach ($transactions as $transaction)
                @php $totalAmount += $transaction->foodOrder->amount_to_pay; @endphp
                <tr>
                    <td>{{ ucfirst($transaction->foodOrder->food->name) }}</td>
                    <td style="text-align: right;font-weight: bold; font-family: 'DejaVu Sans', sans-serif;">
                        ₱ {{ $transaction->foodOrder->food->price }}</td>
                    <td>{{ ucfirst($transaction->foodOrder->quantity) }}</td>
                    <td style="text-align: right;font-weight: bold; font-family: 'DejaVu Sans', sans-serif;">
                        ₱ {{ ucfirst($transaction->foodOrder->amount_to_pay) }}</td>
                </tr>
            @endforeach
            <tr style="font-weight: bold;">
                <td colspan="3" style="text-align: right;">Total Amount:</td>
                <td style="text-align: right; font-family: 'DejaVu Sans', sans-serif;">
                    ₱ {{ number_format($totalAmount, 2) }}
                </td>
            </tr>
        </tbody>
    </table>
</body>

</html>
