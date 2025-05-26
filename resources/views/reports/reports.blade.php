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
                <th>Room Type</th>
                <th>Room Name</th>
                <th>Booking Type</th>
                <th>Booking Date</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody>
            @php $totalAmount = 0; @endphp
            @foreach ($transactions as $transaction)
                @php $totalAmount += $transaction->booking->amount_to_pay; @endphp
                <tr>
                    <td>{{ ucfirst($transaction->booking->room->name) }}</td>
                    <td>{{ ucfirst($transaction->booking->suiteRoom->name) }}</td>
                    <td>{{ ucfirst($transaction->booking->type) }}</td>
                    <td>{{ \Carbon\Carbon::parse($transaction->booking->start_date)->format('F j, Y g:i A') }}</td>
                    <td style="text-align: right;font-weight: bold; font-family: 'DejaVu Sans', sans-serif;">
                        ₱ {{ number_format($transaction->booking->amount_to_pay, 2) }}</td>
                </tr>
            @endforeach
            <tr style="font-weight: bold;">
                <td colspan="4" style="text-align: right;">Total Amount:</td>
                <td style="text-align: right; font-family: 'DejaVu Sans', sans-serif;">
                    ₱ {{ number_format($totalAmount, 2) }}
                </td>
            </tr>
        </tbody>
    </table>
</body>

</html>
