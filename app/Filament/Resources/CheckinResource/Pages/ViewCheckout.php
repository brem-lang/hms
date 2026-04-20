<?php

namespace App\Filament\Resources\CheckinResource\Pages;

use App\Filament\Resources\CheckinResource;
use App\Mail\MailFrontDesk;
use App\Models\Booking;
use App\Models\Charge;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class ViewCheckout extends Page
{
    public $record;

    protected static string $resource = CheckinResource::class;

    protected static string $view = 'filament.resources.checkin-resource.pages.view-checkout';

    public function getTitle(): string
    {
        return 'Check-Out Details - '.$this->record->booking_number;
    }

    public function mount(Booking $record): void
    {
        $this->record = $record;
    }

    public function add_personAction(): Action
    {
        return Action::make('add_person')
            ->icon('heroicon-o-user-plus')
            ->label('Add Person')
            ->color('warning')
            ->visible(fn (): bool => (int) $this->record->is_occupied === 1 && (int) $this->record->room_id === 4)
            ->form([
                TextInput::make('no_persons')
                    ->numeric()
                    ->label('Number of Persons')
                    ->default(fn (): mixed => $this->record->additional_persons)
                    ->required()
                    ->maxLength(255),
            ])
            ->modalWidth('lg')
            ->action(function (array $data): void {
                $record = $this->record->fresh();

                $packageArray = json_decode($record->selected_package, true);
                $itemName = $packageArray['item'] ?? null;

                $chargeID = match ($itemName) {
                    'Basic Package - Option 1',
                    'Basic Package - Option 2' => 5,
                    'Standard Package - Option 1',
                    'Standard Package - Option 2' => 6,
                    'Premium Package - Option 1',
                    'Premium Package - Option 2' => 7,
                    default => 5,
                };

                $charge = Charge::find($chargeID);

                if (! $charge) {
                    Notification::make()
                        ->danger()
                        ->title('Error')
                        ->body('Charge could not be resolved for this package.')
                        ->send();

                    return;
                }

                $newExtendCharge = [
                    'name' => (string) $charge->id,
                    'amount' => number_format($charge->amount, 2, '.', ''),
                    'quantity' => $data['no_persons'],
                    'total_charges' => $charge->amount * $data['no_persons'],
                ];

                $existingCharges = $record->additional_charges ?? [];

                if (! is_array($existingCharges)) {
                    $existingCharges = [];
                }

                $existingCharges[] = $newExtendCharge;

                $record->additional_charges = $existingCharges;
                $record->additional_persons = $data['no_persons'];
                $record->save();

                $this->record = $record;

                Notification::make()
                    ->success()
                    ->title('Person Added')
                    ->send();
            });
    }

    public function extendAction(): Action
    {
        return Action::make('extend')
            ->icon('heroicon-o-clock')
            ->label('Extend')
            ->color('warning')
            ->visible(fn (): bool => (int) $this->record->is_occupied === 1 && (int) $this->record->is_extend === 0)
            ->form(function (): array {
                $record = $this->record;

                $extendField = ($record->duration ?? 0) < 12
                    ? TimePicker::make('extend_date')
                        ->label('Extend Date')
                        ->default(now())
                        ->formatStateUsing(fn (): mixed => $record->extend_date)
                        ->required()
                    : DateTimePicker::make('extend_date')
                        ->label('Extend Date')
                        ->date('F d, Y h:i A')
                        ->default(now())
                        ->formatStateUsing(fn (): mixed => $record->extend_date)
                        ->required();

                return [
                    DateTimePicker::make('check_out_date')
                        ->label('Check Out Date')
                        ->date('F d, Y h:i A')
                        ->dehydrated(false)
                        ->readOnly()
                        ->formatStateUsing(fn (): mixed => $record->check_out_date),
                    $extendField,
                ];
            })
            ->modalWidth('lg')
            ->action(function (array $data): void {
                $record = $this->record;

                try {
                    if (empty($data['extend_date'])) {
                        Notification::make()
                            ->danger()
                            ->title('Error')
                            ->body('Extend date is required')
                            ->send();

                        return;
                    }

                    $extendDate = ($record->duration ?? 0) < 12
                        ? Carbon::parse($record->check_out_date)->setTimeFromTimeString($data['extend_date'])
                        : Carbon::parse($data['extend_date']);
                    $checkOutDate = Carbon::parse($record->check_out_date);

                    if ($extendDate->lessThanOrEqualTo($checkOutDate)) {
                        Notification::make()
                            ->danger()
                            ->title('Error')
                            ->body('Extend date must be after the check out date')
                            ->send();

                        return;
                    }

                    if (CheckinResource::extendChecker($record->room_id, $record->check_out_date, $extendDate->toDateTimeString(), $record->id)) {
                        Notification::make()
                            ->danger()
                            ->title('Error')
                            ->body('Please select different dates for extending')
                            ->send();

                        return;
                    }

                    $diffHours = (int) abs($extendDate->diffInHours($checkOutDate));
                    $extendDateString = $extendDate->toDateTimeString();

                    DB::transaction(function () use ($record, $diffHours, $extendDateString) {
                        $record->refresh();

                        if ($record->room_id != 4) {
                            $extendCharge = Charge::find(2);

                            if (! $extendCharge) {
                                throw new \Exception('Extend charge not found (ID: 2)');
                            }

                            $newExtendCharge = [
                                'name' => (string) $extendCharge->id,
                                'amount' => number_format($extendCharge->amount, 2, '.', ''),
                                'quantity' => (string) $diffHours,
                                'total_charges' => $extendCharge->amount * $diffHours,
                            ];

                            $existingCharges = $record->additional_charges ?? [];

                            if (! is_array($existingCharges)) {
                                $existingCharges = [];
                            }

                            $existingCharges[] = $newExtendCharge;
                            $record->additional_charges = $existingCharges;
                            $record->is_extend = 1;
                            $record->extend_date = $extendDateString;
                            $record->save();
                        } elseif ($record->room_id == 4) {
                            $extendCharge = Charge::find(4);

                            if (! $extendCharge) {
                                throw new \Exception('Extend charge not found (ID: 4)');
                            }

                            $newExtendCharge = [
                                'name' => (string) $extendCharge->id,
                                'amount' => number_format($extendCharge->amount, 2, '.', ''),
                                'quantity' => (string) $diffHours,
                                'total_charges' => $extendCharge->amount * $diffHours,
                            ];

                            $existingCharges = $record->additional_charges ?? [];

                            if (! is_array($existingCharges)) {
                                $existingCharges = [];
                            }

                            $existingCharges[] = $newExtendCharge;
                            $record->additional_charges = $existingCharges;
                            $record->is_extend = 1;
                            $record->extend_date = $extendDateString;
                            $record->save();
                        }
                    });

                    $this->record = $record->fresh();

                    Notification::make()
                        ->success()
                        ->title('Booking Extended')
                        ->send();
                } catch (\Exception $e) {
                    Notification::make()
                        ->danger()
                        ->title('Error')
                        ->body('Failed to extend booking: '.$e->getMessage())
                        ->send();
                }
            });
    }

    public function checkOut()
    {
        $chargesAmount = 0;
        foreach ($this->record->additional_charges ?? [] as $charge) {
            $chargesAmount += $charge['total_charges'];
        }

        $foodChargesAmount = 0;
        foreach ($this->record->food_charges ?? [] as $charge) {
            $foodChargesAmount += $charge['total_charges'];
        }

        $this->record->status = 'done';
        $this->record->is_occupied = 0;
        $this->record->balance = 0;
        $this->record->amount_paid = $this->record->amount_to_pay + $chargesAmount + $foodChargesAmount;
        $this->record->amount_to_pay = $this->record->amount_to_pay + $chargesAmount + $foodChargesAmount;
        $this->record->suiteRoom->is_occupied = 0;
        $this->record->suiteRoom->save();
        $this->record->save();

        if ($this->record->getBookingHead) {
            $this->record->getBookingHead->update([
                'status' => 'done',
            ]);
        }

        if ($this->record->room_id != 4) {
            $details = [
                'name' => $this->record->user->name,
                'message' => 'You have been checked out successfully. Thank you for choosing us!',
                'amount_paid' => $this->record->amount_paid ?? 0,
                'balance' => $this->record->balance ?? 0,
                'type' => 'check_out',
            ];

            Mail::to($this->record->type == 'online' ? $this->record->user->email : $this->record->walkingGuest->email)->send(new MailFrontDesk($details));
        } else {
            $details = [
                'name' => $this->record->organization.' '.$this->record->position,
                'message' => 'You have been checked out successfully. Thank you for choosing us!',
                'amount_paid' => $this->record->amount_paid ?? 0,
                'balance' => $this->record->balance ?? 0,
                'type' => 'check_out',
            ];
            Mail::to($this->record->email)->send(new MailFrontDesk($details));
        }

        Notification::make()
            ->success()
            ->title('Check Out')
            ->send();

        return redirect(CheckinResource::getUrl('index'));
    }
}
