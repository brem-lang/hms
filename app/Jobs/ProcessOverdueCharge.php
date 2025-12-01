<?php

namespace App\Jobs;

use App\Models\Booking;
use App\Models\Charge;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;

class ProcessOverdueCharge implements ShouldQueue
{
    use Queueable;

    protected $booking;

    // Set the number of times this job should be attempted
    public $tries = 3;

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

        $overdueChargeItem = Charge::find(1);

        if (! $overdueChargeItem) {
            return;
        }

        $totalOverdueHours = (int) abs($now->diffInHours($booking->check_out_date));

        if ($totalOverdueHours === 0) {
            return;
        }

        $chargesArray = $booking->additional_charges ?? [];
        $chargeId = (string) $overdueChargeItem->id;
        $overdueKey = null;

        foreach ($chargesArray as $key => $charge) {
            if ((string) $charge['name'] === $chargeId) {
                $overdueKey = $key;
                break;
            }
        }

        $hoursAlreadyCharged = ($overdueKey !== null)
            ? (int) $chargesArray[$overdueKey]['quantity']
            : 0;

        $newHoursToCharge = $totalOverdueHours - $hoursAlreadyCharged;

        if ($newHoursToCharge <= 0) {
            return;
        }

        DB::transaction(function () use ($booking, $overdueChargeItem, $newHoursToCharge, &$chargesArray, $overdueKey, $chargeId, $hoursAlreadyCharged) {

            $newTotalQuantity = $hoursAlreadyCharged + $newHoursToCharge;
            $newTotalCharges = $overdueChargeItem->amount * $newTotalQuantity;

            if ($overdueKey !== null) {
                $chargesArray[$overdueKey]['quantity'] = (string) $newTotalQuantity;
                $chargesArray[$overdueKey]['total_charges'] = $newTotalCharges;
            } else {
                $chargesArray[] = [
                    'name' => $chargeId,
                    'amount' => number_format($overdueChargeItem->amount, 2, '.', ''),
                    'quantity' => (string) $newTotalQuantity,
                    'total_charges' => $newTotalCharges,
                ];
            }

            $booking->additional_charges = $chargesArray;
            $booking->save();
        });
    }
}
