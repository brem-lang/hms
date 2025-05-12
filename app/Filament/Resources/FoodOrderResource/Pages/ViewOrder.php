<?php

namespace App\Filament\Resources\FoodOrderResource\Pages;

use App\Filament\Resources\FoodOrderResource;
use App\Filament\Resources\MyOrderResource;
use App\Models\FoodOrder;
use App\Models\User;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Form;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;

class ViewOrder extends Page
{
    protected static string $resource = FoodOrderResource::class;

    protected static string $view = 'filament.resources.food-order-resource.pages.view-order';

    public $record;

    public ?array $formData = [];

    public function mount(FoodOrder $record): void
    {
        $this->form->fill([
            'proof_of_payment' => $record->proof_of_payment,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                FileUpload::make('proof_of_payment')
                    ->required()
                    ->label('Proof of Payment')
                    ->openable()
                    ->disk('public_uploads_payment')
                    ->directory('/')
                    ->image()
                    ->hint('Please upload the proof of payment for gcash.'),
            ])
            ->statePath('formData');
    }

    public function infoList(Infolist $infolist): Infolist
    {
        return $infolist
            ->record($this->record)
            ->schema([
                TextEntry::make('user.name'),
                TextEntry::make('user.contact_number')
                    ->label('Contact Number'),
                TextEntry::make('status')
                    ->label('')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'gray',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                    })
                    ->formatStateUsing(fn (string $state): string => __(ucfirst($state))),
                TextEntry::make('notes'),
                TextEntry::make('amount_to_pay')->label('Amount To Pay')->prefix('₱ '),
            ])
            ->columns(3);
    }

    public function confirm()
    {
        $this->record->status = 'completed';

        $this->record->save();

        Notification::make()
            ->success()
            ->title('Order Confirmed')
            ->icon('heroicon-o-check-circle')
            ->send();

        Notification::make()
            ->success()
            ->title('Payment Confirmed')
            ->icon('heroicon-o-check-circle')
            ->body($this->record->user->name.' your order has been confirmed')
            ->actions([
                Action::make('view')
                    ->label('View')
                    ->url(fn () => MyOrderResource::getUrl('payment', ['record' => $this->record->id]))
                    ->markAsRead(),
            ])
            ->sendToDatabase(User::where('id', $this->record->user_id)->get());
    }
}
