<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Payment Receipt</title>
    <style>
        body {
            font-family: 'Times New Roman', serif;
            /* Closer to the font used in the sample */
            font-size: 10pt;
            margin: 0;
            padding: 20px;
            color: #333;
        }

        .receipt-container {
            max-width: 650px;
            margin: 0 auto;
            padding: 0;
        }

        .header-title {
            text-align: center;
            font-size: 18pt;
            font-weight: bold;
            color: #cc0000;
            /* Red accent color */
            padding-bottom: 2px;
            margin-bottom: 20px;
        }

        .header-info {
            display: flex;
            justify-content: space-between;
            font-size: 9pt;
            margin-bottom: 30px;
            text-align: right;
        }

        .header-info .transaction-ref {
            color: #cc0000;
            font-weight: bold;
        }

        .section-header {
            text-align: center;
            font-size: 12pt;
            font-weight: bold;
            margin: 15px 0 10px 0;
        }

        .detail-line {
            display: flex;
            margin: 3px 0;
        }

        .detail-line span {
            width: 255px;
            /* Width for the label */
            font-weight: bold;
            display: inline-block;
        }

        .data-input {
            border-bottom: 1px solid #000;
            flex-grow: 1;
            padding-left: 5px;
            font-weight: normal;
        }

        /* --- Payment Table Styles --- */
        .payment-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10pt;
            margin-top: 15px;
        }

        .payment-table th,
        .payment-table td {
            border: 1px solid #333;
            padding: 5px 8px;
            text-align: left;
        }

        .payment-table th {
            text-align: center;
            background-color: #f0f0f0;
        }

        .subtotal-area {
            clear: both;
            width: 100%;
            margin-top: 10px;
            display: flex;
            justify-content: flex-end;
            /* Push table to the right */
        }

        .subtotal-table {
            width: 40%;
            border-collapse: collapse;
            font-size: 10pt;
        }

        .subtotal-table td {
            border: 1px solid #333;
            padding: 3px 8px;
            text-align: right;
            width: 52%;
        }

        .subtotal-table .label {
            width: 65%;
            text-align: left;
            border-right: none;
            font-weight: bold;
        }

        .total-row {
            font-weight: bold;
            background-color: #f0f0f0;
        }

        .signature-area {
            margin-top: 30px;
            font-size: 9pt;
        }

        .signature-line {
            display: inline-block;
            width: 250px;
            border-bottom: 1px solid #000;
            margin-top: 30px;
        }
    </style>
</head>

<body style="font-family: 'DejaVu Sans', sans-serif">

    <div class="receipt-container">

        {{-- HEADER / TRANSACTION INFO --}}
        <div class="header-title">EVENT PAYMENT RECEIPT</div>
        <div class="header-info">
            <div>Date: **{{ Carbon\Carbon::parse($booking->created_at)->format('M d, Y') }}**</div>
            <div>Transaction/Receipt #: <span class="transaction-ref">**{{ $booking->booking_number }}**</span></div>
        </div>

        {{-- EVENT INFORMATION --}}
        <div class="section-header">Event Information</div>
        <div class="guest-info" style="margin-bottom: 30px;">
            <p class="detail-line"><span>Event Date:</span> <span
                    class="data-input">{{ \Carbon\Carbon::parse($booking->check_in_date)->format('M d, Y') }}</span>
            </p>
            <p class="detail-line"><span>Event Time:</span> <span
                    class="data-input">{{ \Carbon\Carbon::parse($booking->check_in_date)->format('F j D, g:i a') }} to
                    {{ \Carbon\Carbon::parse($booking->check_out_date)->format('F j D, g:i a') }}</span>
            </p>
            <p class="detail-line"><span>Event Name:</span> <span
                    class="data-input">{{ ucfirst($booking->event_type) }}</span>
            </p>
            <p class="detail-line"><span>Event Location:</span> <span
                    class="data-input">{{ $booking->suiteRoom->name }}</span></p>
        </div>

        {{-- PAYMENT INFORMATION TABLE --}}
        <div class="section-header">Payment Information</div>
        @php
            // --- Data Preparation Block ---
            // 1. Separate extend charges from other charges
            $extendCharges = collect([]);
            $combinedCharges = collect([]);

            // Determine the target bookings (handle single or bulk bookings)
            $targetBookings = $booking->type != 'bulk_head_online' ? collect([$booking]) : $booking->relatedBookings;

            // Extend charge IDs: 2 for regular rooms, 4 for function hall (room_id == 4)
            $extendChargeIds = [2, 4];

            foreach ($targetBookings as $b) {
                $allCharges = array_merge($b->additional_charges ?? [], $b->food_charges ?? []);

                foreach ($allCharges as $charge) {
                    $chargeId = $charge['name'] ?? null;
                    
                    // Check if this is an extend charge
                    if (in_array($chargeId, $extendChargeIds)) {
                        $extendCharges->push([
                            'name' => $charges[$chargeId] ?? 'Extension Charge',
                            'qty' => $charge['quantity'] ?? 1,
                            'amount' => $charge['amount'] ?? 0,
                            'total_charges' => $charge['total_charges'] ?? 0,
                            'room_name' => $b->suiteRoom->name ?? 'N/A',
                        ]);
                    } else {
                        // Regular additional charges
                        $combinedCharges->push([
                            'name' => $charges[$chargeId] ?? 'Unknown Charge',
                            'qty' => $charge['quantity'] ?? 1,
                            'amount' => $charge['amount'] ?? 0,
                            'total_charges' => $charge['total_charges'] ?? 0,
                            'room_name' => $b->suiteRoom->name ?? 'N/A',
                        ]);
                    }
                }
            }

            $dynamicRowCount = $combinedCharges->count() + $extendCharges->count();
            $placeholderRowCount = max(0, 7 - (1 + $dynamicRowCount)); // Ensure at least 7 rows total (1 base + dynamic + placeholders)
            
            // Calculate base package amount (amount_to_pay)
            $basePackageAmount = $booking->type != 'bulk_head_online' 
                ? $booking->amount_to_pay 
                : $booking->relatedBookings->sum('amount_to_pay');
        @endphp

        <table class="payment-table">
            <thead>
                <tr>
                    <th style="width: 5%;">QTY</th>
                    <th style="width: 55%;">Event Fee/Description</th>
                    <th style="width: 20%;">Unit Price</th>
                    <th style="width: 20%;">Total</th>
                </tr>
            </thead>
            <tbody>

                {{-- 1. BASE PACKAGE CHARGE (ALWAYS PRESENT) --}}
                @php
                    $packageArray = json_decode($booking->selected_package, true);

                    $itemName = $packageArray['item'] ?? 'Base Package Rental';
                    $itemPrice = $packageArray['price'] ?? 0;
                @endphp
                <tr>
                    <td style="text-align: center;">-</td>
                    <td style="font-style: italic; padding-left: 15px;">
                        {{ $booking->suiteRoom->name }}
                    </td>
                    <td style="text-align: right;">₱
                        {{ number_format($basePackageAmount, 2) }}
                    </td>
                    <td style="text-align: right;">₱
                        {{ number_format($basePackageAmount, 2) }}
                    </td>
                </tr>
                @if ($booking->food_corkage == 'yes')
                    <tr>
                        <td style="text-align: center;">-</td>
                        <td style="font-style: italic; padding-left: 15px;">
                            {{ $itemName }}
                        </td>
                        <td style="text-align: right;">₱
                            {{ number_format($itemPrice == 0 ? $booking->suiteRoom->price : $itemPrice, 2) }}
                        </td>
                        <td style="text-align: right;">₱
                            {{ number_format($itemPrice == 0 ? $booking->suiteRoom->price : $itemPrice, 2) }}
                        </td>
                    </tr>
                @endif

                {{-- 2. EXTEND CHARGES (SEPARATE ROWS) --}}
                @foreach ($extendCharges as $charge)
                    <tr>
                        <td style="text-align: center;">{{ $charge['qty'] }}</td>
                        <td style="font-style: italic; padding-left: 15px;">
                            {{ $charge['name'] }}
                            @if ($booking->type == 'bulk_head_online')
                                <span style="font-size: 8pt; color: #888;"> ({{ $charge['room_name'] }})</span>
                            @endif
                        </td>
                        <td style="text-align: right;">₱ {{ number_format($charge['amount'], 2) }}</td>
                        <td style="text-align: right;">₱ {{ number_format($charge['total_charges'], 2) }}</td>
                    </tr>
                @endforeach

                {{-- 3. OTHER ADDITIONAL CHARGES --}}
                @foreach ($combinedCharges as $charge)
                    <tr>
                        <td style="text-align: center;">{{ $charge['qty'] }}</td>
                        <td style="font-style: italic; padding-left: 15px;">
                            {{ $charge['name'] }}
                            @if ($booking->type == 'bulk_head_online')
                                <span style="font-size: 8pt; color: #888;"> ({{ $charge['room_name'] }})</span>
                            @endif
                        </td>
                        <td style="text-align: right;">₱ {{ number_format($charge['amount'], 2) }}</td>
                        <td style="text-align: right;">₱ {{ number_format($charge['total_charges'], 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="subtotal-area"
            style="clear: both; /* Ensure it doesn't overlap previous content */
                                 float: right; /* ⬅️ FIX: Pushes the block to the right */
                                 width: 40%; /* Maintain a reasonable width */
                                 margin-top: 10px;">
            @php
                // Calculate subtotal: amount_to_pay (base package) + all additional charges
                $extendChargesTotal = $extendCharges->sum('total_charges');
                $additionalChargesTotal = $combinedCharges->sum('total_charges');
                $subtotal = $basePackageAmount + $extendChargesTotal + $additionalChargesTotal;
                $finalTotal = $subtotal;
            @endphp

            <table class="subtotal-table" style="width: 100%; border-collapse: collapse; font-size: 10pt;">
                <tbody>

                    <tr>
                        <td class="label" style="font-weight: normal;">Subtotal</td>
                        <td style="border-left: 1px solid #333; text-align: right;">₱ {{ number_format($subtotal, 2) }}
                        </td>
                    </tr>

                    <tr class="total-row">
                        <td class="label" style="font-weight: bold;">Total</td>
                        <td style="border-left: 1px solid #333; font-weight: bold; text-align: right;">₱
                            {{ number_format($finalTotal, 2) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div style="clear: both;"></div> {{-- IMPORTANT: Clear the float after the table --}}

        <div class="footer-payment">
            <p><strong>Paid by:</strong>
                @php
                    // Get the payment method string from your model
                    $paymentMethod = $booking->payment_type ?? '';
                    // Convert to lowercase for reliable comparison
                    $paymentMethod = strtolower($paymentMethod);
                @endphp

                <input type="checkbox" @checked($paymentMethod === 'cash')> Cash
                <input type="checkbox" @checked($paymentMethod === 'gcash')> Gcash
                <input type="checkbox"> Other: ________________________
            </p>
        </div>

        {{-- AUTHORIZED SIGNATURE --}}
        <div class="signature-area">
            <p><strong>Authorized Signature:</strong> <span class="signature-line"></span></p>
            <p style="margin-top: 15px;">Representative's Name: {{ Auth::user()->name }}</p>
            <p>Title: Customer Service Agent</p>
        </div>
    </div>
</body>

</html>
