<?php

namespace App\Jobs;

use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcessNoShowBooking implements ShouldQueue
{
    use Queueable;

    protected $booking;

    /**
     * Create a new job instance.
     */
    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $booking = $this->booking;

        $now = Carbon::now()->timezone('Asia/Manila');
        // $now = Carbon::create(2025, 12, 2, 9, 0, 0, 'Asia/Manila');

        DB::transaction(function () use ($booking, $now) {

            // Always retrieve the latest data to prevent race conditions
            $booking->refresh();

            $checkInDate = Carbon::parse($booking->check_in_date);
            $checkOutDate = Carbon::parse($booking->check_out_date);

            // --- PART 1: Assign 'no show - call' (2 hours past check-in) ---
            if (! $booking->is_no_show) {
                // for hourly
                if ($checkInDate->copy()->addHours(2)->lessThanOrEqualTo($now) && $booking->hours < 24) {
                    $booking->status = 'done';
                    $booking->is_occupied = false;

                    // Free the suite room inventory
                    $booking->suiteRoom->is_occupied = 0;
                    $booking->suiteRoom->save();

                    $booking->save();
                }
                // for daily
                if ($checkInDate->copy()->addHours(1)->lessThanOrEqualTo($now)) {

                    $booking->is_no_show = true;
                    $booking->save();

                    Log::info("Booking {$booking->id} status set to 'no show - call'.");

                    return;
                }
            }

            // --- PART 2: Auto-Checkout for Unresponsive 2-Day Bookings ---
            if ($booking->is_no_show) {

                // 2. Check if the current time is beyond the original check-in date + 2 days
                $secondDayThreshold = $checkInDate->copy()->addDays(1);

                if ($now->greaterThanOrEqualTo($secondDayThreshold)) {

                    $booking->status = 'done'; // Finalized status
                    $booking->is_occupied = false;

                    // Free the suite room inventory
                    $booking->suiteRoom->is_occupied = 0;
                    $booking->suiteRoom->save();

                    $booking->save();

                    // If it's part of a bulk booking head, update the head status
                    if ($booking->getBookingHead) {
                        $booking->getBookingHead->update([
                            'status' => 'done',
                        ]);
                    }

                    Log::warning("Booking {$booking->id} auto-checked out due to 2-day no-show policy.");
                }
            }
        });
    }
}
