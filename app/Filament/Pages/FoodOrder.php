<?php

namespace App\Filament\Pages;

use App\Filament\Resources\MyOrderResource;
use App\Models\Food;
use App\Models\FoodOrder as ModelsFoodOrder;
use App\Models\Transaction;
use App\Models\User;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;

class FoodOrder extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.food-order';

    protected static ?string $navigationGroup = 'Food Management';

    public $foods;

    public ?array $data = [];

    public static function canAccess(): bool
    {
        return false;
    }

    // public static function canAccess(): bool
    // {
    //     return false;
    // }

    public function mount()
    {
        $this->foods = Food::get();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('quantity')
                    ->numeric()
                    ->minValue(0)
                    ->label('Quantity')
                    ->required(),
                Textarea::make('notes')
                    ->label('Notes')
                    ->required()
                    ->maxLength(255),
            ])
            ->statePath('data')
            ->columns(2);
    }

    public function foodOrder(int $id)
    {
        $data = $this->form->getState();

        try {
            DB::beginTransaction();
            $order = ModelsFoodOrder::create([
                'user_id' => auth()->user()->id,
                'food_id' => $id,
                'quantity' => $data['quantity'],
                'notes' => $data['notes'],
                'amount_to_pay' => $this->calculatePrice($id),
                'status' => 'pending',
            ]);

            Transaction::create([
                'food_order_id' => $order->id,
                'type' => 'foods',
            ]);

            DB::commit();

            Notification::make()
                ->success()
                ->title('Food Order Created')
                ->icon('heroicon-o-check-circle')
                ->body('Food order has been created successfully.')
                ->send();

            Notification::make()
                ->success()
                ->title('Food Order Created')
                ->icon('heroicon-o-check-circle')
                ->body(auth()->user()->name.' has booked '.$data->room->name)
                ->actions([
                    Action::make('view')
                        ->label('View')
                    // ->url(fn () => BookingResource::getUrl('view', ['record' => $data->id]))
                    ,
                ])
                ->sendToDatabase(User::whereIn('role', ['admin', 'front-desk'])->get());
        } catch (\Exception $e) {
            logger($e->getMessage());
        }

        redirect(MyOrderResource::getUrl('payment', ['record' => $order->id]));
    }

    public function calculatePrice(int $id)
    {
        $food = Food::find($id);

        $price = $food->price * $this->form->getState()['quantity'];

        return $price;
    }
}
