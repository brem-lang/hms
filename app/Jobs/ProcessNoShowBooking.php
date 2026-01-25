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

        try {
            $now = Carbon::now()->timezone('Asia/Manila');
            // $now = Carbon::create(2025, 12, 2, 9, 0, 0, 'Asia/Manila');

            DB::transaction(function () use ($booking, $now) {

                // Always retrieve the latest data to prevent race conditions
                $booking->refresh();

                // Skip if booking is already checked in - no need to process no-show
                if ($booking->is_occupied == 1) {
                    Log::info("Booking {$booking->id} is already checked in, skipping no-show processing.");
                    return;
                }

                // Skip if booking is already done/settled
                if ($booking->status == 'done') {
                    Log::info("Booking {$booking->id} is already done, skipping no-show processing.");
                    return;
                }

                // Validate dates exist and can be parsed
                if (empty($booking->check_in_date)) {
                    Log::warning("Booking {$booking->id} has no check_in_date, skipping processing.");
                    return;
                }

                if (empty($booking->check_out_date)) {
                    Log::warning("Booking {$booking->id} has no check_out_date, skipping processing.");
                    return;
                }

                try {
                    $checkInDate = Carbon::parse($booking->check_in_date);
                    $checkOutDate = Carbon::parse($booking->check_out_date);
                } catch (\Exception $e) {
                    Log::error("Booking {$booking->id} has invalid date format: ".$e->getMessage());
                    return;
                }

                // --- PART 1: Assign 'no show - call' (2 hours past check-in) ---
                if (! $booking->is_no_show) {
                    // for hourly
                    if ($checkInDate->copy()->addHours(2)->lessThanOrEqualTo($now) && $booking->hours < 24) {
                        $booking->status = 'done';
                        $booking->is_occupied = false;

                        // Free the suite room inventory (only if suiteRoom exists)
                        if ($booking->suiteRoom) {
                            $booking->suiteRoom->is_occupied = 0;
                            $booking->suiteRoom->save();
                        } else {
                            Log::warning("Booking {$booking->id} has no suiteRoom assigned, cannot free room inventory.");
                        }

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

                        // Free the suite room inventory (only if suiteRoom exists)
                        if ($booking->suiteRoom) {
                            $booking->suiteRoom->is_occupied = 0;
                            $booking->suiteRoom->save();
                        } else {
                            Log::warning("Booking {$booking->id} has no suiteRoom assigned, cannot free room inventory.");
                        }

                        $booking->save();

                        // If it's part of a bulk booking head, update the head status
                        $bookingHead = $booking->getBookingHead;
                        if ($bookingHead) {
                            $bookingHead->update([
                                'status' => 'done',
                            ]);
                        }

                        Log::warning("Booking {$booking->id} auto-checked out due to 2-day no-show policy.");
                    }
                }
            });
        } catch (\Exception $e) {
            Log::error("Error processing no-show booking {$booking->id}: ".$e->getMessage(), [
                'exception' => $e,
                'booking_id' => $booking->id ?? null,
            ]);
            throw $e;
        }
    }
}
