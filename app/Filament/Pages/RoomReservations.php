<?php

namespace App\Filament\Pages;

use App\Filament\Resources\BookingResource;
use App\Filament\Resources\MyBookingResource;
use App\Models\Booking;
use App\Models\Room;
use App\Models\User;
use Carbon\Carbon;
use Closure;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
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

    public $record = [];

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.room-reservations';

    public function mount()
    {
        $room = Room::get();

        $this->record = [
            'standard' => $room->where('id', 1)->first(),
            'deluxe' => $room->where('id', 2)->first(),
            'executive' => $room->where('id', 3)->first(),
        ];
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
        ];
    }

    public function standardSuiteForm(Form $form): Form
    {
        return $form->schema([
            DateTimePicker::make('start_date')
                ->required()
                ->minDate(now()->startOfDay())
                ->reactive(),
            DateTimePicker::make('end_date')
                ->required()
                ->minDate(now()->startOfDay())
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
                ])
                ->afterStateUpdated(function ($state, callable $set) {
                    $set('end_date', $state);
                }),
        ])
            ->columns(2)
            ->statePath('standardSuiteData');
    }

    public function deluxeSuiteForm(Form $form): Form
    {
        return $form->schema([
            DateTimePicker::make('start_date')
                ->required()
                ->minDate(now()->startOfDay())
                ->reactive(),
            DateTimePicker::make('end_date')
                ->required()
                ->minDate(now()->startOfDay())
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
                ])
                ->afterStateUpdated(function ($state, callable $set) {
                    $set('end_date', $state);
                }),
        ])
            ->columns(2)
            ->statePath('deluxeSuiteData');
    }

    public function executiveSuiteForm(Form $form): Form
    {
        return $form->schema([
            DateTimePicker::make('start_date')
                ->required()
                ->minDate(now()->startOfDay())
                ->displayFormat('F d, Y h:i A')
                ->seconds(false)
                ->reactive(),
            DateTimePicker::make('end_date')
                ->required()
                ->minDate(now()->startOfDay())
                ->displayFormat('F d, Y h:i A')
                ->seconds(false)
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
                ])
                ->afterStateUpdated(function ($state, callable $set) {
                    $set('end_date', $state);
                }),
        ])
            ->columns(2)
            ->statePath('executiveSuiteData');
    }

    public function standardSuiteSubmit()
    {
        $data = $this->standardSuiteForm->getState();

        $data['suiteId'] = 1;

        $data = $this->saving($data);

        redirect(MyBookingResource::getUrl('payment', ['record' => $data]));
        // redirect('/app/room-reservations');

    }

    public function deluxeSuiteSubmit()
    {
        $data = $this->deluxeSuiteForm->getState();

        $data['suiteId'] = 2;

        $data = $this->saving($data);

        redirect(MyBookingResource::getUrl('payment', ['record' => $data]));
        // redirect('/app/room-reservations');
    }

    public function executiveSuiteSubmit()
    {
        $data = $this->executiveSuiteForm->getState();

        $data['suiteId'] = 3;

        $data = $this->saving($data);

        redirect(MyBookingResource::getUrl('payment', ['record' => $data]));
        // redirect('/app/room-reservations');
    }

    public function saving($data)
    {
        $start = Carbon::parse($data['start_date']);
        $end = Carbon::parse($data['end_date']);

        $seconds = $start->diffInSeconds($end);

        $hours = ceil($seconds / 3600);

        try {
            DB::beginTransaction();

            $data = Booking::create([
                'user_id' => auth()->user()->id,
                'room_id' => $data['suiteId'],
                'status' => 'pending',
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'],
                'duration' => $hours - 1,
                'amount_to_pay' => $this->getPayment($hours - 1, $data['suiteId']),
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
                        ->openUrlInNewTab(),
                ])
                ->sendToDatabase(User::where('role', '!=', 'customer')->get());
        } catch (\Exception $e) {
            DB::rollBack();

            Notification::make()
                ->warning()
                ->title('Booking Failed')
                ->icon('heroicon-o-exclamation-circle')
                ->body('Booking failed: '.$e->getMessage())
                ->send();
        }

        return $data->id;
    }

    public function getPayment($hours, $suiteId)
    {
        logger($suiteId);
        // standard
        if ($suiteId == 1) {
            if ($hours <= 3) {
                return 300;
            } elseif ($hours <= 6) {
                return 500;
            } elseif ($hours <= 12) {
                return 800;
            } else {
                $base = 1200; // Overnight stay
                $extraHours = $hours - 12;
                $extension = $extraHours * 100;

                return $base + $extension;
            }
        }
        // deluxe
        if ($suiteId == 2) {
            if ($hours <= 3) {
                return 350;
            } elseif ($hours <= 6) {
                return 550;
            } elseif ($hours <= 12) {
                return 850;
            } elseif ($hours <= 24) {
                return 1400;
            } else {
                $base = 1400; // Overnight stay
                $extraHours = $hours - 24;
                $extension = $extraHours * 100;

                return $base + $extension;
            }
        }
        // executive
        if ($suiteId == 3) {
            if ($hours <= 3) {
                return 400;
            } elseif ($hours <= 6) {
                return 600;
            } elseif ($hours <= 12) {
                return 900;
            } elseif ($hours <= 24) {
                return 1600;
            } else {
                $base = 1600; // Overnight stay
                $extraHours = $hours - 24;
                $extension = $extraHours * 150;

                return $base + $extension;
            }
        }
    }
}
