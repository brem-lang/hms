<?php

namespace App\Jobs;

use App\Models\Booking;
use App\Models\Room;
use App\Models\SuiteRoom;
use App\Models\Transaction;
use App\Models\WalkinGuest;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ProcessSingleSavingOperation implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(protected array $data, public $type = 'online', public $payment_type = 'gcash')
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $data = $this->data;

        $start = Carbon::parse($data['start_date']);
        $end = $data['suiteId'] == 4 ? $start : Carbon::parse($data['end_date']);

        $days = $start->diffInDays($end);

        $hours = $data['suiteId'] == 4 ? 0 : ($data['hours'] ?? 0) + ($days * 24);

        $booking = Booking::create([
            'payment_type' => $this->payment_type,
            'type' => $this->type,
            'user_id' => $data['userId'],
            'room_id' => $data['suiteId'],
            'status' => 'pending',
            'start_date' => $data['start_date'],
            'check_in_date' => $start->setTime(14, 0)->toDateTimeString(),
            'check_out_date' => $end->setTime(12, 0)->toDateTimeString(),
            'end_date' => $end->toDateTimeString(),
            'duration' => $hours,
            'notes' => $data['notes'],
            'no_persons' => $data['no_persons'],
            'days' => $data['suiteId'] == 4 ? 0 : $days,
            'hours' => $data['suiteId'] == 4 ? 0 : $hours,
            'suite_room_id' => $this->getSuiteRoom($data['suiteId'], $start->setTime(14, 0)->toDateTimeString(), $end->setTime(12, 0)->toDateTimeString()),
            'amount_to_pay' => $this->getPayment($hours, $data['suiteId'], $data['no_persons']),
        ]);

        Transaction::create([
            'booking_id' => $booking->id,
            'type' => 'rooms',
        ]);

        if ($this->type == 'walkin_booking') {
            WalkinGuest::create([
                'booking_id' => $booking->id,
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
            ]);
        }
    }

    public function getSuiteRoom($suiteID, $checkIn, $checkOut)
    {
        $bookedRoomIds = Booking::where('status', '!=', 'cancelled')->where(function ($query) use ($checkIn, $checkOut) {
            $query->where('check_in_date', '<', $checkOut)
                ->where('check_out_date', '>', $checkIn);
        })
            ->pluck('suite_room_id');

        $availableRoom = SuiteRoom::where('room_id', $suiteID)
            ->where('is_active', true)
            ->whereNotIn('id', $bookedRoomIds)
            ->first();

        return $availableRoom?->id ?? false;
    }

    public function getPayment($hours, $suiteId, $no_persons)
    {
        $value = 0;

        $suite = Room::where('id', $suiteId)->first();

        if ($hours <= 3) {
            $value = $suite->items[0]['price'];
        } elseif ($hours <= 6) {
            $value = $suite->items[1]['price'];
        } elseif ($hours <= 12) {
            $value = $suite->items[2]['price'];
        } elseif ($hours <= 24) {
            $value = $suite->items[3]['price'];
        } else {
            if (fmod($hours, 24) == 0.0) {
                $value = ($hours / 24) * $suite->items[3]['price'];
            } else {
                $value = $suite->items[3]['price'] + (($hours - 24) * $suite->items[4]['price']);
            }
        }

        if ($hours > 3 && $hours < 6) {
            $value = $suite->items[0]['price'] + (($hours - 3) * $suite->items[4]['price']);
        } elseif ($hours > 6 && $hours < 12) {
            $value = $suite->items[1]['price'] + (($hours - 6) * $suite->items[4]['price']);
        } elseif ($hours > 12 && $hours < 24) {
            $value = $suite->items[2]['price'] + (($hours - 12) * $suite->items[4]['price']);
        }

        $extraPersons = max(0, $no_persons - 2);
        $extraCharge = $extraPersons * $suite->items[5]['price'];

        return $value + $extraCharge;
    }
}
