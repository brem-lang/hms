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
    <h2>{{ $data['type'] }}</h2>
    {{-- <h4>From {{ \Carbon\Carbon::parse($data['start_date'])->format('F j, Y') }} to
        {{ \Carbon\Carbon::parse($data['end_date'])->format('F j, Y') }}</h4> --}}

    {{-- <table border="1">
        <caption><strong>Room Transactions</strong></caption>
        <thead>
            <tr>
                <th>Room</th>
                <th>Checkin Date</th>
                <th>Checkout Date</th>
                <th>Price</th>
                <th>Amount Paid</th>
            </tr>
        </thead>
        <tbody>
            @php $totalAmount = 0; @endphp
            @foreach ($data['trasanctions'] as $transaction)
                @php $totalAmount += $transaction->booking->amount_to_pay; @endphp
                <tr>
                    <td style="font-size:12px;">{{ ucfirst($transaction->booking->suiteRoom->name) }}</td>
                    <td style="font-size:12px;">
                        {{ \Carbon\Carbon::parse($transaction->booking->check_in_date)->format('F j, Y g:i A') }}
                    </td>
                    <td style="font-size:12px;">
                        {{ \Carbon\Carbon::parse($transaction->booking->check_out_date)->format('F j, Y g:i A') }}
                    </td>
                    <td
                        style="text-align: right;font-weight: bold; font-family: 'DejaVu Sans', sans-serif; font-size:12px;">
                        ₱ {{ number_format($transaction->booking->amount_to_pay, 2) }}</td>
                    <td
                        style="text-align: right;font-weight: bold; font-family: 'DejaVu Sans', sans-serif;  font-size:12px;">
                        ₱ {{ number_format($transaction->booking->amount_paid, 2) }}</td>
                </tr>
            @endforeach
            <tr style="font-weight: bold;">
                <td colspan="4" style="text-align: right; font-size:12px;">Total Amount Paid:</td>
                <td style="text-align: right; font-family: 'DejaVu Sans', sans-serif; font-size:12px;">
                    ₱ {{ number_format($totalAmount, 2) }}
                </td>
            </tr>
        </tbody>
    </table>

    <table border="1" style="margin-top:10px;">
        <caption><strong>Checkout List</strong></caption>
        <thead>
            <tr>
                <th>Room</th>
                <th>Checkin Date</th>
                <th>Checkout Date</th>
                <th>Price</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data['checkout'] as $checkout)
                <tr>
                    <td style="font-size:12px;">{{ ucfirst($checkout->suiteRoom->name) }}</td>
                    <td style="font-size:12px;">
                        {{ \Carbon\Carbon::parse($checkout->check_in_date)->format('F j, Y g:i A') }}
                    </td>
                    <td style="font-size:12px;">
                        {{ \Carbon\Carbon::parse($checkout->check_out_date)->format('F j, Y g:i A') }}
                    </td>
                    <td
                        style="text-align: right;font-weight: bold; font-family: 'DejaVu Sans', sans-serif; font-size:12px;">
                        ₱ {{ number_format($checkout->amount_to_pay, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table border="1" style="margin-top:10px;">
        <caption><strong>Room Charges</strong></caption>
        <thead>
            <tr>
                <th>Room</th>
                <th>Charge Name</th>
                <th>Price</th>
                <th>Amount Paid</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($finalList as $chargeName => $charges)
                @foreach ($charges as $index => $charge)
                    <tr style="font-size: 12px;">
                        <td>{{ $charge['room_name'] }}</td>
                        @if ($index == 0)
                            <td rowspan="{{ count($charges) }}">
                                <strong>{{ $chargeName }}</strong>
                            </td>
                        @endif
                        <td
                            style="text-align: right;font-weight: bold; font-family: 'DejaVu Sans', sans-serif; font-size:12px;">
                            ₱ {{ number_format($charge['amount'], 2) }}</td>
                        <td
                            style="text-align: right;font-weight: bold; font-family: 'DejaVu Sans', sans-serif; font-size:12px;">
                            ₱ {{ number_format($charge['amount'], 2) }}</td>
                    </tr>
                @endforeach
            @endforeach
            <tr style="font-weight: bold;">
                <td colspan="3" style="text-align: right; font-size:12px;">Total Amount Paid:</td>
                <td style="text-align: right; font-family: 'DejaVu Sans', sans-serif; font-size:12px;">
                    ₱ {{ number_format($chargetotalAmount, 2) }}
                </td>
            </tr>
        </tbody>
    </table> --}}

    @if ($data['type'] == 'Sales Reports')
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
                @foreach ($data['trasanctions'] as $transaction)
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
    @endif

    @if ($data['type'] == 'Room Trends Report')
        <table>
            <thead>
                <tr>
                    <th>Room Name</th>
                    <th>Total Bookings</th>
                    <th>Total Sales</th>
                </tr>
            </thead>
            <tbody>
                @php $grandTotal = 0; @endphp
                @foreach ($data['trends'] as $roomName => $stats)
                    <tr>
                        <td>{{ $roomName }}</td>
                        <td>{{ $stats['total_bookings'] }}</td>
                        <td style="text-align: right; font-family: 'DejaVu Sans', sans-serif;">
                            ₱ {{ number_format($stats['total_sales'], 2) }}
                        </td>
                    </tr>
                    @php $grandTotal += $stats['total_sales']; @endphp
                @endforeach
                <tr style="font-weight: bold;">
                    <td colspan="2" style="text-align: right;">Grand Total Sales:</td>
                    <td style="text-align: right; font-family: 'DejaVu Sans', sans-serif;">
                        ₱ {{ number_format($grandTotal, 2) }}
                    </td>
                </tr>
            </tbody>
        </table>
    @endif

    <br><br>
    <div style="width: 100%; text-align: right; margin-top: 50px;">
        <p>___________________</p>
        <p style="margin-top: -10px;">
            {{ $data['authorized_name'] }}
        </p>
    </div>
</body>

</html>
