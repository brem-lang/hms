<?php

namespace App\Console\Commands;

use App\Jobs\ProcessNoShowBooking;
use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CheckNoShows extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-no-shows';

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
        $countDispatched = 0;

        Booking::where('status', 'completed')
            ->where('check_in_date', '<', $now)
            ->chunk(500, function ($bookings) use (&$countDispatched) {
                foreach ($bookings as $booking) {
                    ProcessNoShowBooking::dispatch($booking);
                    $countDispatched++;
                }
            });

        $this->info("Dispatched {$countDispatched} jobs to check no-show status.");
    }
}
