<?php

namespace App\Filament\Pages;

use App\Filament\Resources\BookingResource;
use App\Filament\Resources\MyBookingResource;
use App\Jobs\ProcessSingleSavingOperation;
use App\Models\Booking;
use App\Models\Room;
use App\Models\SuiteRoom;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Closure;
use DateTime;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;

class RoomReservations extends Page implements HasForms
{
    use InteractsWithForms;

    public ?array $standardSuiteData = [];

    public ?array $deluxeSuiteData = [];

    public ?array $executiveSuiteData = [];

    public ?array $functionHallData = [];

    public $record = [];

    public $bookingType;

    protected static ?string $navigationGroup = 'Room Management';

    protected static ?string $navigationIcon = 'heroicon-o-book-open';

    protected static string $view = 'filament.pages.room-reservations';

    public function mount()
    {
        $room = Room::with('suite_rooms')->get();

        $this->record = [
            'standard' => $room->where('id', 1)->first(),
            'deluxe' => $room->where('id', 2)->first(),
            'executive' => $room->where('id', 3)->first(),
            'functionHall' => $room->where('id', 4)->first(),
            'standardOccupied' => $room->where('id', 1)->first()->suite_rooms->where('is_occupied', 0)
                ->count(),
            'deluxeOccupied' => $room->where('id', 2)->first()->suite_rooms->where('is_occupied', 0)
                ->count(),
            'executiveOccupied' => $room->where('id', 3)->first()->suite_rooms->where('is_occupied', 0)
                ->count(),
            'functionHallOccupied' => $room->where('id', 4)->first()->suite_rooms->where('is_occupied', 0)
                ->count(),
        ];

        $this->standardSuiteForm->fill([
            'bookingType' => 'daily',
            'quantity' => 1,
            'start_date' => now()->startOfDay(),
            'end_date' => now()->startOfDay()->addDay(),
            'hour_date' => now()->startOfDay(),
            'at' => now()->startOfDay()->setHour(6),
            'end' => now()->startOfDay()->setHour(11),
            'no_persons' => 2,
        ]);

        $this->deluxeSuiteForm->fill([
            'bookingType' => 'daily',
            'quantity' => 1,
            'start_date' => now()->startOfDay(),
            'end_date' => now()->startOfDay()->addDay(),
            'hour_date' => now()->startOfDay(),
            'at' => now()->startOfDay()->setHour(6),
            'end' => now()->startOfDay()->setHour(11),
            'no_persons' => 2,
        ]);

        $this->executiveSuiteForm->fill([
            'bookingType' => 'daily',
            'quantity' => 1,
            'start_date' => now()->startOfDay(),
            'end_date' => now()->startOfDay()->addDay(),
            'hour_date' => now()->startOfDay(),
            'at' => now()->startOfDay()->setHour(6),
            'end' => now()->startOfDay()->setHour(11),
            'no_persons' => 2,
        ]);
    }

    public static function canAccess(): bool
    {
        return auth()->user()->isCustomer();
    }

    protected function getForms(): array
    {
        return [
            'standardSuiteForm',
            'deluxeSuiteForm',
            'executiveSuiteForm',
            'functionHallForm',
        ];
    }

    public function standardSuiteForm(Form $form): Form
    {
        return $form->schema([
            Select::make('bookingType')
                ->label('Booking Type')
                ->options([
                    'hourly' => 'Hourly',
                    'daily' => 'Daily',
                ])
                ->reactive()
                ->live()
                ->required(),
            TextInput::make('quantity')
                ->label('Quantity')
                ->minValue(0)
                ->numeric()
                ->visible(function (Get $get, Set $set) {
                    if ($get('bookingType') == 'hourly') {
                        return false;
                    } else {
                        return true;
                    }
                })
                ->required()
                ->maxLength(255),
            DatePicker::make('start_date')
                ->required()
                ->minDate(now()->startOfDay())
                ->live()
                ->visible(function (Get $get, Set $set) {
                    if ($get('bookingType') == 'hourly') {
                        return false;
                    } else {
                        return true;
                    }
                })
                ->reactive(),

            DatePicker::make('end_date')
                ->required()
                ->minDate(now()->startOfDay())
                ->live()
                ->reactive()
                ->visible(function (Get $get, Set $set) {
                    if ($get('bookingType') == 'hourly') {
                        return false;
                    } else {
                        return true;
                    }
                })
                ->rules([
                    function (callable $get) {
                        return function (string $attribute, $value, Closure $fail) use ($get) {

                            $date1 = Carbon::createFromFormat('m/d/Y H:i:s', date('m/d/Y H:i:s', strtotime($get('start_date'))));
                            $date2 = Carbon::createFromFormat('m/d/Y H:i:s', date('m/d/Y H:i:s', strtotime($value)));

                            $result = $date1->gte($date2);

                            if ($result) {
                                $fail('End Date must be ahead from Start Date');
                            }
                        };
                    },
                ]),
            DatePicker::make('hour_date')
                ->label('Date')
                ->required()
                ->visible(function (Get $get, Set $set) {
                    if ($get('bookingType') == 'hourly') {
                        return true;
                    } else {
                        return false;
                    }
                })
                ->seconds(false)
                ->reactive()
                ->minDate(now()->startOfDay()),
            TimePicker::make('at')
                ->label('Start Time')
                ->prefixIcon('heroicon-m-play')
                ->visible(function (Get $get, Set $set) {
                    if ($get('bookingType') == 'hourly') {
                        return true;
                    } else {
                        return false;
                    }
                })
                ->format('H:i:s')
                ->displayFormat('h:i A')
                ->seconds(false)
                ->live()
                ->afterStateUpdated(function (Get $get, Set $set) {
                    $set('end', $get('end'));
                }),
            TimePicker::make('end')
                ->label('End Time')
                ->prefixIcon('heroicon-m-play')
                ->visible(function (Get $get, Set $set) {
                    if ($get('bookingType') == 'hourly') {
                        return true;
                    } else {
                        return false;
                    }
                })
                ->format('H:i:s')
                ->displayFormat('h:i A')
                ->seconds(false)
                ->live()
                ->rules([
                    'after:at',
                    function (Get $get) {
                        return function (string $attribute, $value, Closure $fail) use ($get) {
                            $startTime = $get('at');
                            $endTime = $value;

                            if (empty($startTime) || empty($endTime)) {
                                return;
                            }

                            $startCarbon = Carbon::parse($startTime);
                            $endCarbon = Carbon::parse($endTime);

                            if ($endCarbon->lessThanOrEqualTo($startCarbon)) {
                                $fail('The :attribute must be after '.Carbon::parse($startTime)->format('h:i A').'.');
                            }
                        };
                    },
                ]),
            TextInput::make('no_persons')
                ->numeric()
                ->label('Persons')
                ->required()
                ->maxLength(255),
            Textarea::make('notes')
                ->label('Requests / Notes'),
        ])
            ->columns(2)
            ->statePath('standardSuiteData');
    }

    public function deluxeSuiteForm(Form $form): Form
    {
        return $form->schema([
            Select::make('bookingType')
                ->label('Booking Type')
                ->options([
                    'hourly' => 'Hourly',
                    'daily' => 'Daily',
                ])
                ->reactive()
                ->live()
                ->required(),
            TextInput::make('quantity')
                ->label('Quantity')
                ->minValue(0)
                ->numeric()
                ->visible(function (Get $get, Set $set) {
                    if ($get('bookingType') == 'hourly') {
                        return false;
                    } else {
                        return true;
                    }
                })
                ->required()
                ->maxLength(255),
            DatePicker::make('start_date')
                ->required()
                ->minDate(now()->startOfDay())
                ->live()
                ->visible(function (Get $get, Set $set) {
                    if ($get('bookingType') == 'hourly') {
                        return false;
                    } else {
                        return true;
                    }
                })
                ->reactive(),

            DatePicker::make('end_date')
                ->required()
                ->minDate(now()->startOfDay())
                ->live()
                ->reactive()
                ->visible(function (Get $get, Set $set) {
                    if ($get('bookingType') == 'hourly') {
                        return false;
                    } else {
                        return true;
                    }
                })
                ->rules([
                    function (callable $get) {
                        return function (string $attribute, $value, Closure $fail) use ($get) {

                            $date1 = Carbon::createFromFormat('m/d/Y H:i:s', date('m/d/Y H:i:s', strtotime($get('start_date'))));
                            $date2 = Carbon::createFromFormat('m/d/Y H:i:s', date('m/d/Y H:i:s', strtotime($value)));

                            $result = $date1->gte($date2);

                            if ($result) {
                                $fail('End Date must be ahead from Start Date');
                            }
                        };
                    },
                ]),
            DatePicker::make('hour_date')
                ->label('Date')
                ->required()
                ->visible(function (Get $get, Set $set) {
                    if ($get('bookingType') == 'hourly') {
                        return true;
                    } else {
                        return false;
                    }
                })
                ->seconds(false)
                ->reactive()
                ->minDate(now()->startOfDay()),
            TimePicker::make('at')
                ->label('Start Time')
                ->prefixIcon('heroicon-m-play')
                ->visible(function (Get $get, Set $set) {
                    if ($get('bookingType') == 'hourly') {
                        return true;
                    } else {
                        return false;
                    }
                })
                ->format('H:i:s')
                ->displayFormat('h:i A')
                ->seconds(false)
                ->live()
                ->afterStateUpdated(function (Get $get, Set $set) {
                    $set('end', $get('end'));
                }),
            TimePicker::make('end')
                ->label('End Time')
                ->prefixIcon('heroicon-m-play')
                ->visible(function (Get $get, Set $set) {
                    if ($get('bookingType') == 'hourly') {
                        return true;
                    } else {
                        return false;
                    }
                })
                ->format('H:i:s')
                ->displayFormat('h:i A')
                ->seconds(false)
                ->live()
                ->rules([
                    'after:at',
                    function (Get $get) {
                        return function (string $attribute, $value, Closure $fail) use ($get) {
                            $startTime = $get('at');
                            $endTime = $value;

                            if (empty($startTime) || empty($endTime)) {
                                return;
                            }

                            $startCarbon = Carbon::parse($startTime);
                            $endCarbon = Carbon::parse($endTime);

                            if ($endCarbon->lessThanOrEqualTo($startCarbon)) {
                                $fail('The :attribute must be after '.Carbon::parse($startTime)->format('h:i A').'.');
                            }
                        };
                    },
                ]),
            TextInput::make('no_persons')
                ->numeric()
                ->label('Persons')
                ->required()
                ->maxLength(255),
            Textarea::make('notes')
                ->label('Requests / Notes'),
        ])
            ->columns(2)
            ->statePath('deluxeSuiteData');
    }

    public function executiveSuiteForm(Form $form): Form
    {
        return $form->schema([
            Select::make('bookingType')
                ->label('Booking Type')
                ->options([
                    'hourly' => 'Hourly',
                    'daily' => 'Daily',
                ])
                ->reactive()
                ->live()
                ->required(),
            TextInput::make('quantity')
                ->label('Quantity')
                ->minValue(0)
                ->numeric()
                ->visible(function (Get $get, Set $set) {
                    if ($get('bookingType') == 'hourly') {
                        return false;
                    } else {
                        return true;
                    }
                })
                ->required()
                ->maxLength(255),
            DatePicker::make('start_date')
                ->required()
                ->minDate(now()->startOfDay())
                ->live()
                ->visible(function (Get $get, Set $set) {
                    if ($get('bookingType') == 'hourly') {
                        return false;
                    } else {
                        return true;
                    }
                })
                ->reactive(),

            DatePicker::make('end_date')
                ->required()
                ->minDate(now()->startOfDay())
                ->live()
                ->reactive()
                ->visible(function (Get $get, Set $set) {
                    if ($get('bookingType') == 'hourly') {
                        return false;
                    } else {
                        return true;
                    }
                })
                ->rules([
                    function (callable $get) {
                        return function (string $attribute, $value, Closure $fail) use ($get) {

                            $date1 = Carbon::createFromFormat('m/d/Y H:i:s', date('m/d/Y H:i:s', strtotime($get('start_date'))));
                            $date2 = Carbon::createFromFormat('m/d/Y H:i:s', date('m/d/Y H:i:s', strtotime($value)));

                            $result = $date1->gte($date2);

                            if ($result) {
                                $fail('End Date must be ahead from Start Date');
                            }
                        };
                    },
                ]),
            DatePicker::make('hour_date')
                ->label('Date')
                ->required()
                ->visible(function (Get $get, Set $set) {
                    if ($get('bookingType') == 'hourly') {
                        return true;
                    } else {
                        return false;
                    }
                })
                ->seconds(false)
                ->reactive()
                ->minDate(now()->startOfDay()),
            TimePicker::make('at')
                ->label('Start Time')
                ->prefixIcon('heroicon-m-play')
                ->visible(function (Get $get, Set $set) {
                    if ($get('bookingType') == 'hourly') {
                        return true;
                    } else {
                        return false;
                    }
                })
                ->format('H:i:s')
                ->displayFormat('h:i A')
                ->seconds(false)
                ->live()
                ->afterStateUpdated(function (Get $get, Set $set) {
                    $set('end', $get('end'));
                }),
            TimePicker::make('end')
                ->label('End Time')
                ->prefixIcon('heroicon-m-play')
                ->visible(function (Get $get, Set $set) {
                    if ($get('bookingType') == 'hourly') {
                        return true;
                    } else {
                        return false;
                    }
                })
                ->format('H:i:s')
                ->displayFormat('h:i A')
                ->seconds(false)
                ->live()
                ->rules([
                    'after:at',
                    function (Get $get) {
                        return function (string $attribute, $value, Closure $fail) use ($get) {
                            $startTime = $get('at');
                            $endTime = $value;

                            if (empty($startTime) || empty($endTime)) {
                                return;
                            }

                            $startCarbon = Carbon::parse($startTime);
                            $endCarbon = Carbon::parse($endTime);

                            if ($endCarbon->lessThanOrEqualTo($startCarbon)) {
                                $fail('The :attribute must be after '.Carbon::parse($startTime)->format('h:i A').'.');
                            }
                        };
                    },
                ]),
            TextInput::make('no_persons')
                ->numeric()
                ->label('Persons')
                ->required()
                ->maxLength(255),
            Textarea::make('notes')
                ->label('Requests / Notes'),
        ])
            ->columns(2)
            ->statePath('executiveSuiteData');
    }

    public function functionHallForm(Form $form): Form
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
            ->statePath('functionHallData');
    }

    public function processData() {}

    public function standardSuiteSubmit()
    {
        if (empty($this->record['standard']['items'])) {
            Notification::make()
                ->danger()
                ->title('Error')
                ->body('No Rates Found')
                ->send();

            return;
        }

        $data = $this->standardSuiteForm->getState();
        $data['suiteId'] = 1;
        $data['userId'] = auth()->user()->id;

        if ($data['bookingType'] == 'daily') {
            $start = Carbon::parse($data['start_date']);
            $end = $data['suiteId'] == 4 ? $start : Carbon::parse($data['end_date']);
            if ($data['quantity'] == 1) {
                $data = $this->saving($data);
            } else {
                for ($i = 0; $i < $data['quantity']; $i++) {
                    if ($this->getSuiteRoom($data['suiteId'], $start->setTime(14, 0)->toDateTimeString(), $end->setTime(12, 0)->toDateTimeString()) === false) {
                        Notification::make()
                            ->danger()
                            ->title('Error')
                            ->body('No Available Room')
                            ->send();

                        return null;
                    } else {
                        ProcessSingleSavingOperation::dispatch($data);
                    }
                }

                return redirect('/app/my-bookings');
            }
        } else {
            $data = $this->savingHourly($data);
        }

        if ($data) {
            redirect(MyBookingResource::getUrl('payment', ['record' => $data]));
        }
    }

    public function deluxeSuiteSubmit()
    {
        if (empty($this->record['deluxe']['items'])) {
            Notification::make()
                ->danger()
                ->title('Error')
                ->body('No Rates Found')
                ->send();

            return;
        }

        $data = $this->deluxeSuiteForm->getState();

        $data['suiteId'] = 2;
        $data['userId'] = auth()->user()->id;

        if ($data['bookingType'] == 'daily') {
            $start = Carbon::parse($data['start_date']);
            $end = $data['suiteId'] == 4 ? $start : Carbon::parse($data['end_date']);
            if ($data['quantity'] == 1) {
                $data = $this->saving($data);
            } else {
                for ($i = 0; $i < $data['quantity']; $i++) {
                    if ($this->getSuiteRoom($data['suiteId'], $start->setTime(14, 0)->toDateTimeString(), $end->setTime(12, 0)->toDateTimeString()) === false) {
                        Notification::make()
                            ->danger()
                            ->title('Error')
                            ->body('No Available Room')
                            ->send();

                        return null;
                    } else {
                        ProcessSingleSavingOperation::dispatch($data);
                    }
                }

                return redirect('/app/my-bookings');
            }
        } else {
            $data = $this->savingHourly($data);
        }

        if ($data) {
            redirect(MyBookingResource::getUrl('payment', ['record' => $data]));
        }
    }

    public function executiveSuiteSubmit()
    {
        if (empty($this->record['executive']['items'])) {
            Notification::make()
                ->danger()
                ->title('Error')
                ->body('No Rates Found')
                ->send();

            return;
        }

        $data = $this->executiveSuiteForm->getState();

        $data['suiteId'] = 3;
        $data['userId'] = auth()->user()->id;

        if ($data['bookingType'] == 'daily') {
            $start = Carbon::parse($data['start_date']);
            $end = $data['suiteId'] == 4 ? $start : Carbon::parse($data['end_date']);
            if ($data['quantity'] == 1) {
                $data = $this->saving($data);
            } else {
                for ($i = 0; $i < $data['quantity']; $i++) {
                    if ($this->getSuiteRoom($data['suiteId'], $start->setTime(14, 0)->toDateTimeString(), $end->setTime(12, 0)->toDateTimeString()) === false) {
                        Notification::make()
                            ->danger()
                            ->title('Error')
                            ->body('No Available Room')
                            ->send();

                        return null;
                    } else {
                        ProcessSingleSavingOperation::dispatch($data);
                    }
                }

                return redirect('/app/my-bookings');
            }
        } else {
            $data = $this->savingHourly($data);
        }

        if ($data) {
            redirect(MyBookingResource::getUrl('payment', ['record' => $data]));
        }
    }

    public function functionHallSuiteSubmit()
    {
        $data = $this->functionHallForm->getState();

        $data['suiteId'] = 4;

        $data = $this->saving($data);

        if ($data) {
            redirect(MyBookingResource::getUrl('payment', ['record' => $data]));
        }
    }

    public function savingHourly($data)
    {
        $startDateTimeString = $data['hour_date'].' '.$data['at'];
        $endDateTimeString = $data['hour_date'].' '.$data['end'];

        $start = new DateTime($startDateTimeString);
        $end = new DateTime($endDateTimeString);

        $interval = $start->diff($end);

        $hours = $interval->h;

        if ($this->getSuiteRoomHours($data['suiteId'], $start, $end) == false) {
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
                    'start_date' => $data['hour_date'],
                    'check_in_date' => $start,
                    'check_out_date' => $end,
                    'end_date' => $data['hour_date'],
                    'duration' => $hours,
                    'notes' => $data['notes'],
                    'no_persons' => $data['no_persons'],
                    'days' => 0,
                    'hours' => $hours,
                    'suite_room_id' => $this->getSuiteRoomHours($data['suiteId'], $start, $end),
                    'amount_to_pay' => $this->getPayment($hours, $data['suiteId'], $data['no_persons']),
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

    public function getSuiteRoomHours($suiteID, $checkIn, $checkOut)
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
