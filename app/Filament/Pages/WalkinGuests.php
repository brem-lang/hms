<?php

namespace App\Filament\Pages;

use App\Filament\Resources\BookingResource;
use App\Models\Booking;
use App\Models\Room;
use App\Models\SuiteRoom;
use App\Models\Transaction;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;

class WalkinGuests extends Page implements HasForms
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.walkin-guests';

    use InteractsWithForms;

    public ?array $standardSuiteData = [];

    public ?array $deluxeSuiteData = [];

    public ?array $executiveSuiteData = [];

    public ?array $functionHallData = [];

    public $record = [];

    public function mount()
    {
        $room = Room::with('suite_rooms')->get();

        $this->record = [
            'standard' => $room->where('id', 1)->first(),
            'deluxe' => $room->where('id', 2)->first(),
            'executive' => $room->where('id', 3)->first(),
            'functionHall' => $room->where('id', 4)->first(),
        ];
    }

    public static function canAccess(): bool
    {
        return ! auth()->user()->isCustomer();
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
            DatePicker::make('start_date')
                ->required()
                ->minDate(now()->startOfDay())
                ->live()
                ->reactive(),
            DatePicker::make('end_date')
                ->live()
                ->required()
                ->minDate(now()->startOfDay()),
            Select::make('hours')
                ->reactive()
                ->live()
                ->required(function (Get $get) {
                    $start = $get('start_date');
                    $end = $get('end_date');

                    if (! $start || ! $end) {
                        return false;
                    }

                    $start = \Carbon\Carbon::parse($start);
                    $end = \Carbon\Carbon::parse($end);

                    $days = $start->diffInDays($end);

                    return $days < 1;
                })
                ->hidden(
                    function (Get $get, Set $set) {
                        $start = $get('start_date');
                        $end = $get('end_date');

                        if (! $start || ! $end) {
                            return true;
                        }

                        $start = \Carbon\Carbon::parse($start);
                        $end = \Carbon\Carbon::parse($end);

                        $days = $start->diffInDays($end);

                        return $days >= 1 ? true : false;
                    }
                )
                ->options(function () {
                    $hours = [];
                    for ($i = 1; $i <= 24; $i++) {
                        $hours[$i] = "$i Hrs";
                    }

                    return $hours;
                }),
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
            DatePicker::make('start_date')
                ->required()
                ->minDate(now()->startOfDay())
                ->live()
                ->reactive(),
            DatePicker::make('end_date')
                ->live()
                ->required()
                ->minDate(now()->startOfDay()),
            Select::make('hours')
                ->reactive()
                ->live()
                ->required(function (Get $get) {
                    $start = $get('start_date');
                    $end = $get('end_date');

                    if (! $start || ! $end) {
                        return false;
                    }

                    $start = \Carbon\Carbon::parse($start);
                    $end = \Carbon\Carbon::parse($end);

                    $days = $start->diffInDays($end);

                    return $days < 1;
                })
                ->hidden(
                    function (Get $get, Set $set) {
                        $start = $get('start_date');
                        $end = $get('end_date');

                        if (! $start || ! $end) {
                            return true;
                        }

                        $start = \Carbon\Carbon::parse($start);
                        $end = \Carbon\Carbon::parse($end);

                        $days = $start->diffInDays($end);

                        return $days >= 1 ? true : false;
                    }
                )
                ->options(function () {
                    $hours = [];
                    for ($i = 1; $i <= 24; $i++) {
                        $hours[$i] = "$i Hrs";
                    }

                    return $hours;
                }),
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
            DatePicker::make('start_date')
                ->required()
                ->minDate(now()->startOfDay())
                ->live()
                ->reactive(),
            DatePicker::make('end_date')
                ->live()
                ->required()
                ->minDate(now()->startOfDay()),
            Select::make('hours')
                ->reactive()
                ->live()
                ->required(function (Get $get) {
                    $start = $get('start_date');
                    $end = $get('end_date');

                    if (! $start || ! $end) {
                        return false;
                    }

                    $start = \Carbon\Carbon::parse($start);
                    $end = \Carbon\Carbon::parse($end);

                    $days = $start->diffInDays($end);

                    return $days < 1;
                })
                ->hidden(
                    function (Get $get, Set $set) {
                        $start = $get('start_date');
                        $end = $get('end_date');

                        if (! $start || ! $end) {
                            return true;
                        }

                        $start = \Carbon\Carbon::parse($start);
                        $end = \Carbon\Carbon::parse($end);

                        $days = $start->diffInDays($end);

                        return $days >= 1 ? true : false;
                    }
                )
                ->options(function () {
                    $hours = [];
                    for ($i = 1; $i <= 24; $i++) {
                        $hours[$i] = "$i Hrs";
                    }

                    return $hours;
                }),
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
                ->required()
                ->minDate(now()->startOfDay())
                ->live()
                ->reactive(),
            DatePicker::make('end_date')
                ->live()
                ->required()
                ->minDate(now()->startOfDay()),
            Select::make('hours')
                ->reactive()
                ->live()
                ->required(function (Get $get) {
                    $start = $get('start_date');
                    $end = $get('end_date');

                    if (! $start || ! $end) {
                        return false;
                    }

                    $start = \Carbon\Carbon::parse($start);
                    $end = \Carbon\Carbon::parse($end);

                    $days = $start->diffInDays($end);

                    return $days < 1;
                })
                ->hidden(
                    function (Get $get, Set $set) {
                        $start = $get('start_date');
                        $end = $get('end_date');

                        if (! $start || ! $end) {
                            return true;
                        }

                        $start = \Carbon\Carbon::parse($start);
                        $end = \Carbon\Carbon::parse($end);

                        $days = $start->diffInDays($end);

                        return $days >= 1 ? true : false;
                    }
                )
                ->options(function () {
                    $hours = [];
                    for ($i = 1; $i <= 24; $i++) {
                        $hours[$i] = "$i Hrs";
                    }

                    return $hours;
                }),
            TextInput::make('no_persons')
                ->numeric()
                ->label('Persons')
                ->required()
                ->maxLength(255),
            Textarea::make('notes')
                ->label('Requests / Notes'),
        ])
            ->columns(2)
            ->statePath('functionHallData');
    }

    public function standardSuiteSubmit()
    {
        $data = $this->standardSuiteForm->getState();

        $data['suiteId'] = 1;

        $data = $this->saving($data);

        redirect(BookingResource::getUrl('view', ['record' => $data]));

        // redirect('/app/room-reservations');

    }

    public function deluxeSuiteSubmit()
    {
        $data = $this->deluxeSuiteForm->getState();

        $data['suiteId'] = 2;

        $data = $this->saving($data);

        redirect(BookingResource::getUrl('view', ['record' => $data]));
        // redirect('/app/room-reservations');
    }

    public function executiveSuiteSubmit()
    {
        $data = $this->executiveSuiteForm->getState();

        $data['suiteId'] = 3;

        $data = $this->saving($data);

        redirect(BookingResource::getUrl('view', ['record' => $data]));
        // redirect('/app/room-reservations');
    }

    public function functionHallSuiteSubmit()
    {
        $data = $this->functionHallForm->getState();

        $data['suiteId'] = 4;

        $data = $this->saving($data);

        redirect(BookingResource::getUrl('view', ['record' => $data]));
        // redirect('/app/room-reservations');
    }

    public function saving($data)
    {
        $start = Carbon::parse($data['start_date']);
        $end = Carbon::parse($data['end_date']);

        $days = $start->diffInDays($end);

        $hours = $data['suiteId'] == 4 ? 0 : ($data['hours'] ?? 0) + ($days * 24);

        try {
            DB::beginTransaction();

            $data = Booking::create([
                'type' => 'walkin_booking',
                'user_id' => auth()->user()->id,
                'room_id' => $data['suiteId'],
                'status' => 'pending',
                'start_date' => $data['start_date'],
                'check_in_date' => \Carbon\Carbon::parse($data['start_date'])->setTime(14, 0)->toDateTimeString(),
                'check_out_date' => \Carbon\Carbon::parse($data['end_date'])->setTime(12, 0)->toDateTimeString(),
                'end_date' => $data['end_date'],
                'duration' => $hours,
                'notes' => $data['notes'],
                'no_persons' => $data['no_persons'],
                'days' => $data['suiteId'] == 4 ? 0 : $days,
                'hours' => $data['suiteId'] == 4 ? 0 : $hours,
                'suite_room_id' => $data['suiteId'] == 4 ? $data['type'] : $this->getSuiteRoom($data['suiteId']),
                'amount_to_pay' => $data['suiteId'] == 4 ? SuiteRoom::where('id', $data['type'])->first()->price : $this->getPayment($hours, $data['suiteId'], $data['no_persons']),
            ]);

            Transaction::create([
                'booking_id' => $data->id,
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

        return $data->id;
    }

    public function getSuiteRoom($suiteID)
    {
        $suiteRooms = SuiteRoom::where('is_active', true)
            ->where('is_occupied', false)
            ->where('room_id', $suiteID)
            ->inRandomOrder()
            ->take(1) // Limit to 3 random records
            ->first();

        return $suiteRooms->id;
    }

    public function getPayment($hours, $suiteId, $no_persons)
    {
        $value = 0;

        if ($suiteId == 1) {
            if ($hours <= 3) {
                $value = 300;
            } elseif ($hours <= 6) {
                $value = 500;
            } elseif ($hours <= 12) {
                $value = 800;
            } elseif ($hours <= 24) {
                $value = 1200;
            } else {
                // Check if $hours is a whole multiple of 24 (like 48, 72, etc.)
                if (fmod($hours, 24) == 0.0) {
                    $value = ($hours / 24) * 1200;
                } else {
                    // Base for 24 hours, add ₱100/hour for the extra hours beyond 24
                    $value = 1200 + (($hours - 24) * 100);
                }
            }

            // Add ₱100 for each hour beyond the fixed tier, only for under 24 hrs
            if ($hours > 3 && $hours < 6) {
                $value = 300 + (($hours - 3) * 100);
            } elseif ($hours > 6 && $hours < 12) {
                $value = 500 + (($hours - 6) * 100);
            } elseif ($hours > 12 && $hours < 24) {
                $value = 800 + (($hours - 12) * 100);
            }
        }
        // deluxe
        if ($suiteId == 2) {
            if ($hours <= 3) {
                $value = 350;
            } elseif ($hours <= 6) {
                $value = 550;
            } elseif ($hours <= 12) {
                $value = 850;
            } elseif ($hours <= 24) {
                $value = 1400;
            } else {
                // Check if $hours is a whole multiple of 24 (like 48, 72, etc.)
                if (fmod($hours, 24) == 0.0) {
                    $value = ($hours / 24) * 1400;
                } else {
                    // Base for 24 hours, add ₱100/hour for the extra hours beyond 24
                    $value = 1400 + (($hours - 24) * 100);
                }
            }

            // Add ₱100 for each hour beyond the fixed tier, only for under 24 hrs
            if ($hours > 3 && $hours < 6) {
                $value = 350 + (($hours - 3) * 100);
            } elseif ($hours > 6 && $hours < 12) {
                $value = 550 + (($hours - 6) * 100);
            } elseif ($hours > 12 && $hours < 24) {
                $value = 850 + (($hours - 12) * 100);
            }
        }
        // executive
        if ($suiteId == 3) {
            if ($hours <= 3) {
                $value = 400;
            } elseif ($hours <= 6) {
                $value = 600;
            } elseif ($hours <= 12) {
                $value = 900;
            } elseif ($hours <= 24) {
                $value = 1600;
            } else {
                // Base for full 24 hours
                $value = 1600;
            }

            // Add ₱100 for each hour beyond the fixed tier
            if ($hours > 3 && $hours < 6) {
                $value = 400 + (($hours - 3) * 100);
            } elseif ($hours > 6 && $hours < 12) {
                $value = 600 + (($hours - 6) * 100);
            } elseif ($hours > 12 && $hours < 24) {
                $value = 900 + (($hours - 12) * 100);
            } elseif ($hours > 24) {
                $value = 1600 + (($hours - 24) * 100);
            }

            // return $value;

            if ($hours <= 3) {
                $value = 400;
            } elseif ($hours <= 6) {
                $value = 600;
            } elseif ($hours <= 12) {
                $value = 900;
            } elseif ($hours <= 24) {
                $value = 1600;
            } else {
                // Check if $hours is a whole multiple of 24 (like 48, 72, etc.)
                if (fmod($hours, 24) == 0.0) {
                    $value = ($hours / 24) * 1600;
                } else {
                    // Base for 24 hours, add ₱100/hour for the extra hours beyond 24
                    $value = 1600 + (($hours - 24) * 100);
                }
            }

            // Add ₱100 for each hour beyond the fixed tier, only for under 24 hrs
            if ($hours > 3 && $hours < 6) {
                $value = 400 + (($hours - 3) * 100);
            } elseif ($hours > 6 && $hours < 12) {
                $value = 600 + (($hours - 6) * 100);
            } elseif ($hours > 12 && $hours < 24) {
                $value = 900 + (($hours - 12) * 100);
            }
        }

        $extraPersons = max(0, $no_persons - 2);
        $extraCharge = $extraPersons * 700;

        return $value + $extraCharge;
    }
}
