<?php

namespace App\Filament\Pages;

use App\Filament\Resources\BookingResource;
use App\Models\Booking;
use App\Models\Charge;
use App\Models\Room;
use App\Models\SuiteRoom;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Closure;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\HtmlString;

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

    public $activePage = 'home';

    public function book()
    {
        $this->activePage = 'book';
    }

    public function cancel()
    {
        $this->activePage = 'home';
    }

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
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        TextInput::make('organization')
                            ->label('First Name')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('position')
                            ->label('Last Name')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('contact_number')
                            ->numeric()
                            ->label('Contact Number')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('email')
                            ->label('Email')
                            ->required()
                            ->maxLength(255),
                        DateTimePicker::make('start_date')
                            ->label('Start Date')
                            ->required()
                            ->minDate(now()->startOfDay())
                            ->reactive(),
                        DateTimePicker::make('end_date')
                            ->label('End Date')
                            ->required()
                            ->minDate(now()->startOfDay())
                            ->reactive(),
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
                        Radio::make('food_corkage')
                            ->columnSpanFull()
                            ->columns(2)
                            ->required()
                            ->live()
                            ->label('Event Package (Catering & Decoration)')
                            ->options([
                                'yes' => 'Yes, I will avail the All-In Package',
                                'no' => 'No, I will arrange my own catering and decoration',
                            ]),
                        Select::make('selected_package')
                            ->label('Select Event Package Option')
                            ->hidden(fn (callable $get) => $get('food_corkage') == 'no')
                            ->options(function () {
                                // Assume you need to fetch the structured data from a specific room (ID 4)
                                $functionHall = Room::find(4);

                                if (! $functionHall || empty($functionHall->items) || ! is_array($functionHall->items)) {
                                    return []; // Return empty array if data is missing or malformed
                                }

                                $options = [];
                                $itemsData = $functionHall->items; // The array of packages

                                foreach ($itemsData as $data) {

                                    // Ensure price is treated as a clean number
                                    $price = (int) str_replace(',', '', $data['price']);
                                    $item = $data['item'];

                                    // 1. Define the VALUE TO BE STORED (JSON string of the item and price)
                                    $valueToStore = json_encode([
                                        'item' => $item,
                                        'price' => $price,
                                    ]);

                                    // 2. Define the USER-FRIENDLY LABEL (for the dropdown display)
                                    $userLabel = $item.' (₱'.number_format($price, 2).')';

                                    // Add to the options array: [VALUE => LABEL]
                                    $options[$valueToStore] = $userLabel;
                                }

                                return $options;
                            })
                            // ->searchable()
                            ->required()
                            ->placeholder('Choose a package option')
                            ->columnSpanFull(),
                        Select::make('type') // Renamed to 'suite_room_id' for clarity, as it's the ID being passed
                            ->label('Select Function Hall / Room')
                            ->required()
                            ->reactive()
                            ->live()
                            ->options(SuiteRoom::where('room_id', 4)->pluck('name', 'id')->toArray())

                            // 🛑 LOGIC TO AUTO-POPULATE PERSONS 🛑
                            ->afterStateUpdated(function (?string $state, callable $set) {
                                // $state holds the selected Suite Room ID
                                if ($state) {
                                    $maxPersons = match ((int) $state) { // Cast $state to int for safety
                                        28 => 15,
                                        29 => 20,
                                        30 => 30,
                                        31 => 40,
                                        default => null,
                                    };

                                    // Set the value of the 'no_persons' field
                                    $set('no_persons', $maxPersons);
                                } else {
                                    // Clear the field if the selection is cleared
                                    $set('no_persons', null);
                                }
                            }),

                        TextInput::make('no_persons')
                            ->numeric()
                            ->label('Number of Persons')
                            ->required()
                            ->maxLength(255)

                            // Optional: Add a validation rule to ensure the user doesn't exceed the max
                            ->rules([
                                function (callable $get) {
                                    return function (string $attribute, $value, Closure $fail) use ($get) {
                                        $typeId = $get('suite_room_id');
                                        if (! $typeId) {
                                            return;
                                        }

                                        $max = match ((int) $typeId) {
                                            28 => 15,
                                            29 => 20,
                                            30 => 30,
                                            31 => 40,
                                            default => 9999, // High value if ID not matched
                                        };

                                        if ($value > $max) {
                                            $fail("The number of persons cannot exceed the maximum capacity of {$max}.");
                                        }
                                    };
                                },
                            ]),
                        Textarea::make('notes')
                            ->label('Requests / Notes'),

                        Placeholder::make('terms_and_conditions_content')
                            ->content(function () {

                                $htmlContent = '<div style="max-height: 200px; overflow-y: scroll; border: 1px solid #ccc; padding: 15px; background-color: #f9f9f9; font-size: 0.9em;">
            <h3>Function Hall Booking Terms (Summary)</h3>
            <ol>
                <li>A non-refundable 50% down payment is required for confirmation.</li>
                <li>Balance is due 7 days prior to the event date.</li>
                <li>Overtime charges 1000 PHP per hour.</li>
                <li>The client is fully liable for any damages to the property.</li>
            </ol>
            <p style="font-weight: bold; margin-top: 10px;">Please read the full contract before proceeding.</p>
        </div>';

                                // 🛑 THE FIX: Wrap the HTML content
                                return new HtmlString($htmlContent);
                            })
                            ->columnSpanFull(),

                        // 2. Required Consent Checkbox
                        Checkbox::make('agreed_to_terms')
                            ->label('I have read and agree to the Terms and Conditions.')
                            ->validationMessages([
                                'accepted' => 'You must agree to the terms and conditions to proceed.',
                            ])
                            ->accepted() // Ensures the checkbox MUST be checked (true)
                            ->required(),
                    ])
                    ->columns(2),
            ])
            ->statePath('data');
    }

    public function functionHallSuiteSubmit()
    {
        $data = $this->form->getState();

        $data['suiteId'] = 4;

        unset($data['agreed_to_terms']);
        unset($data['terms_and_conditions_content']);

        $data = $this->saving($data);

        if ($data) {
            redirect(BookingResource::getUrl('view', ['record' => $data]));
        }
    }

    public function saving($data)
    {
        $start = Carbon::parse($data['start_date']);
        $end = Carbon::parse($data['end_date']);

        $days = $start->diffInDays($end);

        $hours = ($data['hours'] ?? 0) + ($days * 24);

        if ($hours > 4) {

            Notification::make()
                ->title('Error')
                ->body('The max number of hours is 4')
                ->danger()
                ->send();

            return;
        }

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

            // $this->form->fill();

            return;
        }

        if ($maxPersons && $noPersons > $maxPersons) {
            Notification::make()
                ->title('Error')
                ->body("The number of persons cannot exceed {$maxPersons} for the selected room type.")
                ->danger()
                ->send();

            // $this->form->fill();

            return; // stop saving
        }

        if ($this->getFunctionHallTime($start->toDateTimeString(), $end->toDateTimeString(), $data['type']) === false) {
            Notification::make()
                ->danger()
                ->title('Error')
                ->body('Function Hall is fully booked')
                ->send();

            return null;
        }

        if ($this->getFunctionHallTime($start->toDateTimeString(), $end->toDateTimeString(), $data['type'])) {
            Notification::make()
                ->danger()
                ->title('Error')
                ->body('The selected time is not available')
                ->send();

            return null;
        } else {
            try {
                DB::beginTransaction();

                $data = Booking::create([
                    'booking_number' => 'BKG-'.strtoupper(uniqid()),
                    'contact_number' => $data['contact_number'],
                    'food_corkage' => $data['food_corkage'] ?? null,
                    'selected_package' => $data['selected_package'] ?? null,
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
                    'check_in_date' => $start->toDateTimeString(),
                    'check_out_date' => $end->toDateTimeString(),
                    'end_date' => $end->toDateTimeString(),
                    'duration' => $hours,
                    'notes' => $data['notes'],
                    'no_persons' => $data['no_persons'],
                    'days' => $data['suiteId'] == 4 ? 0 : $days,
                    'hours' => $data['suiteId'] == 4 ? 0 : $hours,
                    'suite_room_id' => $data['type'],
                    'amount_to_pay' => $this->getPayment($data['selected_package'] ?? null, $data['food_corkage'], $data['type']),
                    'food_corkage_amount' => $data['food_corkage'] == 'no' ? 1000 : null,
                    'additional_charges' => $data['food_corkage'] == 'no' ? $this->corkage() : null,
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
            } catch (\Exception $e) {
                DB::rollBack();

                logger($e->getMessage());
            }

            return $data?->id;
        }
    }

    public function corkage()
    {
        $charge = Charge::find(8);

        return [
            [
                'name' => $charge->id,
                'amount' => number_format($charge->amount, 2, '.', ''),
                'quantity' => 1,
                'total_charges' => $charge->amount,
            ],
        ];
    }

    public function getPayment($package, $corkage, $type)
    {
        if ($corkage == 'yes') {
            $package = json_decode($package);

            return $package->price;
        }

        if ($corkage = 'no') {
            return SuiteRoom::where('id', $type)->first()->price;
        }
    }

    public function getFunctionHallTime($checkIn, $checkOut, $suiteRoomId)
    {
        $newCheckInDate = Carbon::parse($checkIn);
        $newCheckOutDate = Carbon::parse($checkOut);

        $bookings = Booking::where('suite_room_id', $suiteRoomId)
            ->where('room_id', 4)
            ->where('status', 'completed')
            ->where('type', '!=', 'bulk_head_online')
            ->whereDate('check_in_date', '<=', $newCheckInDate)
            ->whereDate('check_out_date', '>=', $newCheckOutDate)
            ->get();

        if ($bookings->count() >= 2) {
            return false;
        }

        $checkInDateOnly = Carbon::parse($checkIn)->toDateString();

        $precedingBooking = Booking::where('suite_room_id', $suiteRoomId)
            ->where('room_id', 4)
            ->where('status', 'completed')
            ->where('type', '!=', 'bulk_head_online')
            ->whereDate('check_in_date', '<=', $checkInDateOnly)
            ->whereDate('check_out_date', '>=', $checkInDateOnly)
            ->first();

        if ($precedingBooking) {
            $precedingBookingCheckOut = Carbon::parse($precedingBooking->check_out_date);
            $proposedCheckIn = Carbon::parse($checkIn);
            $requiredStartBuffer = $precedingBookingCheckOut->copy()->addHours(2);
            if ($proposedCheckIn->lessThan($requiredStartBuffer)) {
                return true;
            } else {
                return null;
            }
        }
    }
}
