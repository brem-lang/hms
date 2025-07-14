<?php

namespace App\Filament\Pages;

use App\Filament\Resources\BookingResource;
use App\Models\Booking;
use App\Models\Room;
use App\Models\SuiteRoom;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;

class FunctionHallBooking extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.function-hall-booking';

    protected static ?string $navigationGroup = 'Function Hall';

    protected static ?string $title = 'Function Hall';

    public $record;

    public ?array $data = [];

    public function mount()
    {

        $room = Room::with('suite_rooms')->get();

        $this->record = [
            'functionHall' => $room->where('id', 4)->first(),
            'functionHallOccupied' => $room->where('id', 4)->first()->suite_rooms->where('is_occupied', 0)
                ->count(),
        ];
    }

    public static function canAccess(): bool
    {
        return auth()->user()->isCustomer() || auth()->user()->isFrontDesk();
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            DatePicker::make('start_date')
                ->label('Date')
                ->required()
                ->minDate(now()->startOfDay())
                ->live()
                ->reactive(),
            TextInput::make('no_persons')
                ->numeric()
                ->label('Persons')
                ->required()
                ->maxLength(255),
            Select::make('type')
                ->required()
                ->options(SuiteRoom::where('room_id', 4)->pluck('name', 'id')->toArray()),
            Textarea::make('notes')
                ->label('Requests / Notes'),
        ])
            ->columns(2)
            ->statePath('data');
    }

    public function functionHallSuiteSubmit()
    {
        $data = $this->form->getState();

        $data['suiteId'] = 4;

        $data = $this->saving($data);

        if ($data) {
            redirect(BookingResource::getUrl('view', ['record' => $data]));
        }
    }

    public function saving($data)
    {
        $start = Carbon::parse($data['start_date']);
        $end = $data['suiteId'] == 4 ? $start : Carbon::parse($data['end_date']);

        $days = $start->diffInDays($end);

        $hours = $data['suiteId'] == 4 ? 0 : ($data['hours'] ?? 0) + ($days * 24);

        if ($data['suiteId'] == 4 && $this->getFunctionHallTime($start->toDateTimeString(), $data['type']) === false) {
            Notification::make()
                ->danger()
                ->title('Error')
                ->body('Function Hall is fully booked')
                ->send();

            return null;
        }

        if ($data['suiteId'] != 4 && $this->getSuiteRoom($data['suiteId'], $start->setTime(14, 0)->toDateTimeString(), $end->setTime(12, 0)->toDateTimeString()) === false) {
            Notification::make()
                ->danger()
                ->title('Error')
                ->body('No Available Room')
                ->send();

            return null;
        } else {
            try {
                DB::beginTransaction();

                $data = Booking::create([
                    'payment_type' => 'gcash',
                    'type' => 'online',
                    'user_id' => auth()->user()->id,
                    'room_id' => $data['suiteId'],
                    'status' => 'pending',
                    'start_date' => $data['start_date'],
                    'check_in_date' => $data['suiteId'] == 4 ? $this->getFunctionHallTime($start->toDateTimeString(), $data['type'])['start'] : $start->setTime(14, 0)->toDateTimeString(),
                    'check_out_date' => $data['suiteId'] == 4 ? $this->getFunctionHallTime($start->toDateTimeString(), $data['type'])['end'] : $end->setTime(12, 0)->toDateTimeString(),
                    'end_date' => $end->toDateTimeString(),
                    'duration' => $hours,
                    'notes' => $data['notes'],
                    'no_persons' => $data['no_persons'],
                    'days' => $data['suiteId'] == 4 ? 0 : $days,
                    'hours' => $data['suiteId'] == 4 ? 0 : $hours,
                    'suite_room_id' => $data['suiteId'] == 4 ? $data['type'] : $this->getSuiteRoom($data['suiteId'], $start->setTime(14, 0)->toDateTimeString(), $end->setTime(12, 0)->toDateTimeString()),
                    'amount_to_pay' => $data['suiteId'] == 4 ? SuiteRoom::where('id', $data['type'])->first()->price : $this->getPayment($hours, $data['suiteId'], $data['no_persons']),
                ]);

                Transaction::create([
                    'booking_id' => $data->id,
                    'type' => 'rooms',
                ]);

                DB::commit();

                Notification::make()
                    ->success()
                    ->title('Booking Created')
                    ->icon('heroicon-o-check-circle')
                    ->body('Booking has been created successfully.')
                    ->send();

                Notification::make()
                    ->success()
                    ->title('Booking Created')
                    ->icon('heroicon-o-check-circle')
                    ->body(auth()->user()->name.' has booked '.$data->room->name)
                    ->actions([
                        Action::make('view')
                            ->label('View')
                            ->url(fn () => BookingResource::getUrl('view', ['record' => $data->id]))
                            ->markAsRead(),

                    ])
                    ->sendToDatabase(User::whereIn('role', ['admin', 'front-desk'])->get());
            } catch (\Exception $e) {
                DB::rollBack();

                logger($e->getMessage());
            }

            return $data?->id;
        }
    }

    public function getFunctionHallTime($date, $suiteRoomId)
    {
        $start = \Carbon\Carbon::parse($date)->setTime(8, 0);
        $end = $start->copy()->addHours(4);

        $bookings = \App\Models\Booking::where('suite_room_id', $suiteRoomId)
            ->whereDate('start_date', $start->toDateString())
            ->orderBy('start_date')
            ->get();

        if ($bookings->count() >= 2) {
            return false;
        }

        if ($bookings->count() === 1) {
            $lastBooking = $bookings->first();
            $start = \Carbon\Carbon::parse($lastBooking->check_out_date)->addHour();
            $end = $start->copy()->addHours(4);
        }

        return [
            'start' => $start->toDateTimeString(),
            'end' => $end->toDateTimeString(),
        ];
    }
}
