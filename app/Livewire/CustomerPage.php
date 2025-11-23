<?php

namespace App\Livewire;

use App\Filament\Resources\BookingResource;
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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class CustomerPage extends Component implements HasForms
{
    use InteractsWithForms;

    public ?array $standardSuiteData = [];

    public ?array $deluxeSuiteData = [];

    public ?array $executiveSuiteData = [];

    public $record;

    public $activePage = 'home';

    public $selectedRoom;

    public $showForm = false;

    public $notifications;

    public $unreadNotificationsCount;

    public $calendarEvents = [];

    public function render()
    {
        return view('livewire.customer-page');
    }

    public function mount()
    {

        if (Auth::check() && ! auth()->user()->isCustomer()) {
            abort(404);
        }

        $room = Room::with('suite_rooms', 'roomBooking.suiteRoom')->get();

        if (Auth::check()) {
            $this->loadNotifications();
        }

        // dd(Room::with('roomBooking.suiteRoom')
        //     ->find(1)?->roomBooking
        //     ->where('status', 'completed')
        //     ->filter(function ($booking) {
        //         return $booking->suiteRoom &&
        //             $booking->suiteRoom->is_occupied == 1 &&
        //             Carbon::parse($booking->start_date)->isSameDay(Carbon::now('Asia/Manila'));
        //     })
        //     ->count());

        $this->record = [
            'standard' => $room->where('id', 1)->first(),
            'deluxe' => $room->where('id', 2)->first(),
            'executive' => $room->where('id', 3)->first(),
            'functionHall' => $room->where('id', 4)->first(),
            'standardAvailable' => $room->where('id', 1)->first()->suite_rooms->where('is_active', true)->count(),
            'deluxeAvailable' => $room->where('id', 2)->first()->suite_rooms->where('is_active', true)->count(),
            'executiveAvailable' => $room->where('id', 3)->first()->suite_rooms->where('is_active', true)->count(),
            'functionHallAvailable' => $room->where('id', 4)->first()->suite_rooms->where('is_active', true)->count(),
            'standardOccupied' => Booking::whereNotIn('status', ['cancelled', 'done'])
                ->whereHas('suiteRoom', fn ($q) => $q->where('room_id', 1)) // room_id = 1
                ->where('check_in_date', '<=', now('Asia/Manila'))
                ->where('check_out_date', '>', now('Asia/Manila'))
                ->distinct('suite_room_id')
                ->count('suite_room_id'),
            'deluxeOccupied' => Booking::whereNotIn('status', ['cancelled', 'done'])
                ->whereHas('suiteRoom', fn ($q) => $q->where('room_id', 2)) // room_id = 1
                ->where('check_in_date', '<=', now('Asia/Manila'))
                ->where('check_out_date', '>', now('Asia/Manila'))
                ->distinct('suite_room_id')
                ->count('suite_room_id'),
            'executiveOccupied' => Booking::whereNotIn('status', ['cancelled', 'done'])
                ->whereHas('suiteRoom', fn ($q) => $q->where('room_id', 3)) // room_id = 1
                ->where('check_in_date', '<=', now('Asia/Manila'))
                ->where('check_out_date', '>', now('Asia/Manila'))
                ->distinct('suite_room_id')
                ->count('suite_room_id'),
            'functionHallOccupied' => Booking::whereNotIn('status', ['cancelled', 'done'])
                ->whereHas('suiteRoom', fn ($q) => $q->where('room_id', 4)) // room_id = 1
                ->where('check_in_date', '<=', now('Asia/Manila'))
                ->where('check_out_date', '>', now('Asia/Manila'))
                ->distinct('suite_room_id')
                ->count('suite_room_id'),
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

    public function markAllAsRead()
    {
        auth()->user()->unreadNotifications->markAsRead();

        return redirect()->route('index');
    }

    public function clearAll()
    {
        auth()->user()->notifications()->delete();

        return redirect()->route('index');
    }

    public function loadNotifications()
    {
        $this->notifications = auth()->user()
            ->notifications()
            ->take(50)
            ->get();

        $this->unreadNotificationsCount = auth()->user()->unreadNotifications->count();
    }

    protected function getForms(): array
    {
        return [
            'standardSuiteForm',
            'deluxeSuiteForm',
            'executiveSuiteForm',
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
                ->label('Number of Rooms')
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
                ->readOnly()
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
                ->label('Number of Rooms')
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
                ->readOnly()
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
                ->label('Number of Rooms')
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

    public function logout()
    {
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();

        return redirect()->to('/');
    }

    public function viewRoom($room)
    {
        $room = Room::where('id', $room)->first();

        $this->activePage = 'viewRoom';

        $this->selectedRoom = $room;

        $this->calendarEvents = $room->roomBooking
            ->filter(fn ($b) => $b->suiteRoom && $b->suiteRoom->is_occupied && $b->status == 'completed')
            ->map(function ($booking) {
                $color = '#16a34a'; // Default to green

                if ($booking->status === 'pending') {
                    $color = '#f59e0b'; // Yellow for pending
                } elseif ($booking->status === 'cancel') {
                    $color = '#ef4444'; // Red for cancel
                }

                return [
                    'title' => $booking->start_date->format('Y-m-d').' - '.$booking->end_date->format('Y-m-d'),
                    'start' => $booking->start_date->format('Y-m-d').'T'.$booking->start_date->format('H:i:s'),
                    'end' => $booking->end_date->format('Y-m-d').'T'.$booking->end_date->format('H:i:s'),
                    'color' => $color,
                ];
            })
            ->values();

        // if (Auth::check()) {
        // } else {
        //     return redirect('/app/register');
        // }
    }

    public function bookRoom()
    {
        if (! Auth::check()) {
            return redirect('/app/register');
        }

        if ($this->selectedRoom['name'] == 'Standard Suite') {
            $this->standardSuiteSubmit();
        }

        if ($this->selectedRoom['name'] == 'Deluxe Suite') {
            $this->deluxeSuiteSubmit();
        }

        if ($this->selectedRoom['name'] == 'Executive Suite') {
            $this->executiveSuiteSubmit();
        }
    }

    public function standardSuiteSubmit()
    {
        if (empty($this->record['standard']['items'])) {
            $this->dispatch('swal:success', [
                'title' => 'Error',
                'icon' => 'error',
            ]);

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
                $bulk = $this->bulk($data);
                for ($i = 0; $i < $data['quantity']; $i++) {
                    if ($this->getSuiteRoom($data['suiteId'], $start->setTime(14, 0)->toDateTimeString(), $end->setTime(12, 0)->toDateTimeString()) === false) {
                        $this->dispatch('swal:success', [
                            'title' => 'Error',
                            'text' => 'No Available Room.',
                            'icon' => 'error',
                        ]);

                        return null;
                    } else {
                        ProcessSingleSavingOperation::dispatch($data, 'bulk_online', 'gcash', $bulk);
                    }
                }

                $this->dispatch('swal:success', [
                    'title' => 'Submitted ',
                    'icon' => 'success',
                ]);

                return redirect('/view-booking/'.$bulk);
            }
        } else {
            $data = $this->savingHourly($data);
        }

        if ($data) {
            $this->dispatch('swal:success', [
                'title' => 'Submitted ',
                'icon' => 'success',
            ]);

            redirect('/view-booking/'.$data);
        }
    }

    public function deluxeSuiteSubmit()
    {
        if (empty($this->record['deluxe']['items'])) {
            $this->dispatch('swal:success', [
                'title' => 'Error',
                'icon' => 'error',
            ]);

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
                $bulk = $this->bulk($data);
                for ($i = 0; $i < $data['quantity']; $i++) {
                    if ($this->getSuiteRoom($data['suiteId'], $start->setTime(14, 0)->toDateTimeString(), $end->setTime(12, 0)->toDateTimeString()) === false) {
                        $this->dispatch('swal:success', [
                            'title' => 'Error',
                            'text' => 'No Available Room.',
                            'icon' => 'error',
                        ]);

                        return null;
                    } else {
                        ProcessSingleSavingOperation::dispatch($data, 'bulk_online', 'gcash', $bulk);
                    }
                }

                $this->dispatch('swal:success', [
                    'title' => 'Submitted ',
                    'icon' => 'success',
                ]);

                return redirect('/view-booking/'.$bulk);
            }
        } else {
            $data = $this->savingHourly($data);
        }

        if ($data) {
            $this->dispatch('swal:success', [
                'title' => 'Submitted ',
                'icon' => 'success',
            ]);

            redirect('/view-booking/'.$data);
        }
    }

    public function executiveSuiteSubmit()
    {
        if (empty($this->record['executive']['items'])) {
            $this->dispatch('swal:success', [
                'title' => 'Error',
                'icon' => 'error',
            ]);

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
                $bulk = $this->bulk($data);
                for ($i = 0; $i < $data['quantity']; $i++) {
                    if ($this->getSuiteRoom($data['suiteId'], $start->setTime(14, 0)->toDateTimeString(), $end->setTime(12, 0)->toDateTimeString()) === false) {
                        $this->dispatch('swal:success', [
                            'title' => 'Error',
                            'text' => 'No Available Room.',
                            'icon' => 'error',
                        ]);

                        return null;
                    } else {
                        ProcessSingleSavingOperation::dispatch($data, 'bulk_online', 'gcash', $bulk);
                    }
                }

                $this->dispatch('swal:success', [
                    'title' => 'Submitted ',
                    'icon' => 'success',
                ]);

                return redirect('/view-booking/'.$bulk);
            }
        } else {
            $data = $this->savingHourly($data);
        }

        if ($data) {
            $this->dispatch('swal:success', [
                'title' => 'Submitted ',
                'icon' => 'success',
            ]);

            redirect('/view-booking/'.$data);
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

            $this->dispatch('swal:success', [
                'title' => 'Error No Available Room',
                'icon' => 'error',
            ]);

            return null;
        } else {
            try {
                DB::beginTransaction();

                $data = Booking::create([
                    'booking_number' => 'BKG-'.strtoupper(uniqid()),
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

    public function bulk($data)
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
                    'booking_number' => 'BKG-'.strtoupper(uniqid()),
                    'payment_type' => 'gcash',
                    'type' => 'bulk_head_online',
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
                    'suite_room_id' => null,
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

            $this->dispatch('swal:success', [
                'title' => 'Error No Available Room',
                'icon' => 'error',
            ]);

            return null;
        } else {
            try {
                DB::beginTransaction();

                $data = Booking::create([
                    'booking_number' => 'BKG-'.strtoupper(uniqid()),
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

    public function getSuiteRoom($suiteID, $checkIn, $checkOut)
    {
        $bookedRoomIds = Booking::where('status', '!=', 'cancelled')
            ->where('status', '!=', 'done')
            ->where('type', '!=', 'bulk_head_online')
            ->where(function ($query) use ($checkIn, $checkOut) {
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

    // public function getSuiteRoomHours($suiteID, $checkIn, $checkOut)
    // {
    //     // $bookedRoomIds = Booking::where('status', '!=', 'cancelled')->where(function ($query) use ($checkIn, $checkOut) {
    //     //     $query->where('check_in_date', '<', $checkOut)
    //     //         ->where('check_out_date', '>', $checkIn);
    //     // })
    //     //     ->pluck('suite_room_id');
    //     $bookedRoomIds = Booking::where('status', '!=', 'cancelled')
    //         // ->where('status', 'pending')
    //         // ->where('type', '!=', 'bulk_head_online')
    //         // ->where('status', '!=', 'done')
    //         ->whereHas('suiteRoom', fn ($q) => $q->where('is_occupied', false))
    //         // ->where('duration', '<', 24)
    //         ->where(function ($query) use ($checkIn, $checkOut) {
    //             $query->where('check_in_date', '<', $checkOut)
    //                 ->where('check_out_date', '>', $checkIn);
    //         })
    //         ->pluck('suite_room_id');

    //     $availableRoom = SuiteRoom::where('room_id', $suiteID)
    //         ->where('is_active', true)
    //         ->whereNotIn('id', $bookedRoomIds)
    //         ->where('is_occupied', false)
    //         ->first();

    //     return $availableRoom?->id ?? false;
    // }
    public function getSuiteRoomHours($suiteID, $checkIn, $checkOut)
    {
        // Get rooms that have overlapping bookings (hourly or daily)
        $bookedRoomIds = Booking::whereNotIn('status', ['cancelled', 'done'])
            ->where(function ($query) use ($checkIn, $checkOut) {
                $query->where('check_in_date', '<', $checkOut)     // overlaps
                    ->where('check_out_date', '>', $checkIn);
            })
            ->pluck('suite_room_id');

        // Find available room
        return SuiteRoom::where('room_id', $suiteID)
            ->where('is_active', true)
            ->whereNotIn('id', $bookedRoomIds)
            ->value('id') ?? false;
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
