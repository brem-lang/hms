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

    public $calendarEvents = [];

    public function mount()
    {

        $room = Room::with('suite_rooms')->get();

        $this->record = [
            'functionHall' => $room->where('id', 4)->first(),
            'functionHallOccupied' => $room->where('id', 4)->first()->suite_rooms->where('is_occupied', 0)
                ->count(),
        ];

        $this->calendarEvents = Booking::whereNotNull('event_type')
            ->whereNotNull('email')
            ->get()
            ->map(function ($booking) {
                return [
                    // Use properties from the $booking model
                    'title' => $booking->booking_number, // Assuming 'event_type' is the title
                    'start' => $booking->check_in_date, // Assuming these fields exist
                    'end' => $booking->check_out_date,     // Assuming these fields exist
                    'color' => '#2563eb', // Static color is fine
                ];
            });
    }

    public static function canAccess(): bool
    {
        return auth()->user()->isCustomer() || auth()->user()->isFrontDesk();
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('contact_number')
                ->numeric()
                ->label('Contact Number')
                ->required()
                ->maxLength(255),
            TextInput::make('email')
                ->label('Email')
                ->required()
                ->maxLength(255),
            TextInput::make('organization')
                ->label('Organization')
                ->hint('Optional')
                ->maxLength(255),
            TextInput::make('position')
                ->label('Position')
                ->hint('Optional')
                ->maxLength(255),
            Select::make('event_type')
                ->label('Event Type')
                ->required()
                ->options([
                    'wedding' => 'Wedding',
                    'birthday' => 'Birthday',
                    'corporate_event' => 'Corporate Event',
                    'seminar' => 'Seminar',
                    'meeting' => 'Meeting',
                    'others' => 'Others',
                ]),
            DatePicker::make('start_date')
                ->label('Date')
                ->required()
                ->minDate(now()->startOfDay())
                ->live()
                ->reactive(),
            TextInput::make('no_persons')->numeric()->label('Persons')->required()->maxLength(255),
            Select::make('type')
                ->required()
                ->reactive()
                ->live()
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

        $typeId = (int) $this->form->getState()['type'];
        $noPersons = (int) $this->form->getState()['no_persons'];

        $maxPersons = match ($typeId) {
            28 => 15,
            29 => 20,
            30 => 30,
            31 => 40,
            default => null,
        };

        if ($data['no_persons'] > 40) {
            Notification::make()
                ->title('Error')
                ->body('The max number to be input is 40')
                ->danger()
                ->send();

            $this->form->fill();

            return;
        }

        if ($maxPersons && $noPersons > $maxPersons) {
            Notification::make()
                ->title('Error')
                ->body("The number of persons cannot exceed {$maxPersons} for the selected room type.")
                ->danger()
                ->send();

            $this->form->fill();

            return; // stop saving
        }

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
                    'booking_number' => 'BKG-'.strtoupper(uniqid()),
                    'contact_number' => $data['contact_number'],
                    'email' => $data['email'],
                    'organization' => $data['organization'],
                    'position' => $data['position'],
                    'event_type' => $data['event_type'],
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

                // Notification::make()
                //     ->success()
                //     ->title('Booking Created')
                //     ->icon('heroicon-o-check-circle')
                //     ->body(auth()->user()->name.' has booked '.$data->room->name)
                //     ->actions([
                //         Action::make('view')
                //             ->label('View')
                //             ->url(fn () => BookingResource::getUrl('view', ['record' => $data->id]))
                //             ->markAsRead(),

                //     ])
                //     ->sendToDatabase(User::whereIn('role', ['admin', 'front-desk'])->get());
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
