<?php

namespace App\Console\Commands;

use App\Filament\Resources\BookingResource;
use App\Models\Booking;
use App\Models\User;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Console\Command;

class CheckoutAlertCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:checkout-alert-command';

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
        $startWindow = now()->timezone('Asia/Manila')->addMinutes(8);
        $endWindow = now()->timezone('Asia/Manila')->addMinutes(10);

        $bookingsToRemind = Booking::with('user')
            ->where('status', 'completed')
            ->whereBetween('check_out_date', [$startWindow, $endWindow])
            ->get();

        foreach ($bookingsToRemind as $booking) {

            Notification::make()
                ->success()
                ->title('10 Minutes Left to Check Out')
                ->actions([
                    Action::make('view-booking')
                        ->label('View Booking')
                        ->url(BookingResource::getUrl('view', ['record' => $booking->id]))
                        ->openUrlInNewTab(),
                ])
                ->sendToDatabase(User::where('role', '!=', 'customer')->get());
        }
    }
}
