<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Charge;
use Barryvdh\DomPDF\Facade\Pdf;

class BookingController extends Controller
{
    public function downloadChargesPdf(Booking $booking)
    {
        // 1. Prepare Data for the PDF View

        // Get charge lookup tables (These would be loaded from your database)
        $charges = Charge::pluck('name', 'id')->toArray();
        $foodCharges = Charge::pluck('name', 'id')->toArray(); // Assuming food charges use the same lookup table

        // Determine the target bookings (single or bulk)
        $targetBookings = $booking->type != 'bulk_head_online' ? collect([$booking]) : $booking->relatedBookings;

        $booking->load('suiteRoom');

        // dd($booking);
        // 2. Load View and Generate PDF
        $pdf = Pdf::loadView('reports.functionhall', compact('booking', 'targetBookings', 'charges', 'foodCharges'));

        // 3. Stream the PDF for viewing
        return $pdf->stream("charges-{$booking->booking_number}.pdf");
    }
}
