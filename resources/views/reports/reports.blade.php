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

        @font-face {
            font-family: 'DejaVu Sans';
            src: url('{{ public_path('fonts/DejaVuSans.ttf') }}') format('truetype');
            font-weight: normal;
            font-style: normal;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .summary-box {
            border: 1px solid #000;
            padding: 15px;
            margin-bottom: 20px;
        }

        .logo {
            text-align: center;
            margin-bottom: -10px;
        }

        .logo img {
            max-height: 100px;
        }
    </style>
</head>

<body>
    <div class="logo">
        <img src="{{ public_path('images/new logo.png') }}" alt="Logo">
    </div>
    {{-- <h2>{{ $data['type'] }}</h2> --}}
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
        {{-- <table>
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
        </table> --}}
        <div class="header">
            <h2>{{ $data['report_title'] }}</h2>
            <p>Generated By: {{ $data['authorized_name'] }}</p>
            <p>Date Range: **{{ \Carbon\Carbon::parse($data['start_date'])->format('F j, Y') }}** to
                **{{ \Carbon\Carbon::parse($data['end_date'])->format('F j, Y') }}**</p>
        </div>

        <div class="summary-box">
            <h3>Summary Overview</h3>
            <p><strong>Total Revenue:</strong> ₱ {{ number_format($data['total_revenue'], 2) }}</p>
            <p><strong>Total Transactions:</strong> {{ $data['total_transactions'] }}</p>
        </div>

        ---

        @if ($data['sales_by_room_type']->count() > 0)
            <h3>Sales Breakdown by Room Type</h3>
            <p>Visualizing the revenue distribution across different room types.</p>

            <img src="{{ $data['chart_image_url'] }}" alt="Sales Distribution Chart"
                style="width: 90%; margin: 10px auto; border: 1px solid #ccc;">

            <p style="text-align: center;">(Diagram: Bar chart showing revenue contribution per room type)</p>
        @endif

        ---

        <h3>Detailed Transaction List (Total: {{ $data['total_transactions'] }})</h3>
        <table>
            <thead>
                <tr>
                    <th>Room Type</th>
                    <th>Room Name</th>
                    <th>Booking Type</th>
                    <th>Booking Date</th>
                    <th style="text-align: right;">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data['transactions'] as $transaction)
                    <tr>
                        {{-- Check for relationship existence before accessing --}}
                        <td>{{ ucfirst($transaction->booking->room->name ?? 'N/A') }}</td>
                        <td>{{ ucfirst($transaction->booking->suiteRoom->name ?? 'N/A') }}</td>
                        <td>{{ ucfirst($transaction->booking->type) }}</td>
                        <td>{{ \Carbon\Carbon::parse($transaction->booking->start_date)->format('F j, Y g:i A') }}</td>
                        <td style="text-align: right; font-weight: bold;">
                            ₱ {{ number_format($transaction->booking->amount_to_pay, 2) }}
                        </td>
                    </tr>
                @endforeach
                <tr style="font-weight: bold; background-color: #f2f2f2;">
                    <td colspan="4" style="text-align: right;">Total Revenue:</td>
                    <td style="text-align: right;">
                        ₱ {{ number_format($data['total_revenue'], 2) }}
                    </td>
                </tr>
            </tbody>
        </table>
    @endif

    @if ($data['type'] == 'Room Trends Report')
        {{-- <div class="chart-container">
            <h2>Sales Trend Visualization</h2>
            <p>Total Revenue generated by each room type.</p>



            [Image of Sales Breakdown Bar Chart for Hotel Rooms]

            <img src="{{ $data['chart_image_path'] }}" alt="Sales Trend Chart"
                style="width: 90%; margin: 10px auto; border: 1px solid #ccc;">
        </div>
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
        </table> --}}
        <div class="header">
            <h1>{{ $data['report_title'] }}</h1>
            <p>Generated By: **{{ $data['authorized_name'] }}**</p>
            <p>Period: **{{ \Carbon\Carbon::parse($data['start_date'])->format('F j, Y') }}** to
                **{{ \Carbon\Carbon::parse($data['end_date'])->format('F j, Y') }}**</p>
        </div>

        <hr>

        <div style="text-align: center; margin: 40px 0;">
            <div
                style="
        display: inline-block;
        width: 200px;
        padding: 15px;
        margin-right: 20px;
        border-radius: 8px;
        background: #f0fdf4;
        border: 1px solid #86efac;
        text-align: center;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    ">
                <h3 style="margin: 0; font-size: 14pt; color: #065f46;">Total Sales</h3>
                <p style="font-size: 18pt; margin: 10px 0; color: #10b981;">
                    ₱ {{ number_format($data['grand_total_sales'], 2) }}
                </p>
            </div>

            <div
                style="
        display: inline-block;
        width: 200px;
        padding: 15px;
        border-radius: 8px;
        background: #fffbeb;
        border: 1px solid #fcd34d;
        text-align: center;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    ">
                <h3 style="margin: 0; font-size: 14pt; color: #92400e;">Total Bookings</h3>
                <p style="font-size: 18pt; margin: 10px 0; color: #f59e0b;">
                    {{ $data['grand_total_bookings'] }}
                </p>
            </div>
        </div>



        <hr style="clear: both; margin-top: 10px;">

        <h2>Room Utilization & Sales (Monthly)</h2>

        @foreach ($data['room_stats'] as $room)
            <h3 style="margin-top:25px; color:#2563eb;">
                Room: {{ $room['room_name'] }}
            </h3>

            <table>
                <thead>
                    <tr>
                        <th>Month</th>
                        <th>Total Bookings</th>
                        <th>Done</th>
                        <th>Completed</th>
                        <th>Cancelled</th>
                        <th style="text-align:right;">Sales (₱)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($room['monthly'] as $month => $stats)
                        <tr>
                            <td>{{ $month }}</td>
                            <td>{{ $stats['total_bookings'] }}</td>
                            <td>{{ $stats['done'] }}</td>
                            <td>{{ $stats['completed'] }}</td>
                            <td>{{ $stats['cancelled'] }}</td>
                            <td style="text-align:right;">
                                ₱ {{ number_format($stats['sales'], 2) }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div style="text-align:center; margin:20px 0;">
                <h4>Room Utilization Chart</h4>
                <img src="{{ $room['chart'] }}" style="width:90%; border:1px solid #ccc;">
            </div>

            <hr>
        @endforeach


        <div class="chart-container">
            <h2>Sales Performance Trend (Grouped by {{ $data['group_by'] }})</h2>
            <img src="{{ $data['chart_image_path'] }}" alt="Sales Trend Chart"
                style="width: 90%; margin: 10px auto; border: 1px solid #ccc;">
        </div>

        <hr>

        <h2>Detailed Trends Data</h2>
        <table>
            <thead>
                <tr>
                    <th>{{ $data['group_by'] }}</th>
                    <th>Transaction Count</th>
                    <th style="text-align: right;">Total Sales</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data['trends'] as $period => $stats)
                    <tr>
                        <td>{{ $period }}</td>
                        <td>{{ $stats['transaction_count'] }}</td>
                        <td class="currency">
                            ₱ {{ number_format($stats['total_sales'], 2) }}
                        </td>
                    </tr>
                @endforeach
                <tr style="font-weight: bold; background-color: #eef2ff;">
                    <td style="text-align: right; color: #2563eb;">Grand Total:</td>
                    <td style="color: #2563eb;">{{ $data['grand_total_bookings'] }}</td>
                    <td class="currency" style="color: #2563eb;">
                        ₱ {{ number_format($data['grand_total_sales'], 2) }}
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
