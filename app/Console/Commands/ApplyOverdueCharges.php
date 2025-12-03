<?php

namespace App\Console\Commands;

use App\Jobs\ProccessOverDueFunctionHall;
use App\Jobs\ProcessOverdueCharge;
use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ApplyOverdueCharges extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:apply-overdue-charges';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now()->timezone('Asia/Manila');
        $count = 0;

        $this->info("Starting overdue charge check at: {$now->toDateTimeString()}");

        Booking::where('check_out_date', '<', $now)
            ->where('status', 'completed')
            ->where('is_occupied', 1)
            ->where('is_extend', 0)
            ->chunk(500, function ($overdueBookings) use (&$count) {
                foreach ($overdueBookings as $booking) {
                    if ($booking->room_id == 4) {
                        ProccessOverDueFunctionHall::dispatch($booking);
                    } else {
                        ProcessOverdueCharge::dispatch($booking);
                    }

                    $count++;
                }
            });

        $this->comment("Process finished. Total overdue bookings found and charge jobs dispatched: {$count}.");

        return 0;
    }
}
