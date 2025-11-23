<?php

namespace App\Filament\Pages;

use App\Filament\Resources\FoodOrderResource;
use App\Filament\Resources\MyOrderResource;
use App\Models\Food;
use App\Models\FoodOrder;
use App\Models\Transaction;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;

class GuestOrders extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.guest-orders';

    protected static ?string $navigationGroup = 'Entry';

    public $foods;

    public ?array $data = [];

    public static function canAccess(): bool
    {
        return true;
    }

    public function mount()
    {
        $this->foods = Food::get();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('user_id')
                    ->required()
                    ->label('Guest')
                    ->options(User::where('role', 'customer')->get()->pluck('name', 'id')),
                TextInput::make('quantity')
                    ->minValue(0)
                    ->numeric()
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
            $order = FoodOrder::create([
                'user_id' => $data['user_id'],
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
                ->title('You Order'.$order->food->name)
                ->icon('heroicon-o-check-circle')
                ->actions([
                    Action::make('view')
                        ->label('View')
                        ->url(fn () => MyOrderResource::getUrl('payment', ['record' => $order->id]))
                        ->markAsRead(),
                ])
                ->sendToDatabase(User::where('id', $data['id'])->get());
        } catch (\Exception $e) {
            logger($e->getMessage());
        }

        redirect(FoodOrderResource::getUrl('view', ['record' => $order->id]));
    }

    public function calculatePrice(int $id)
    {
        $food = Food::find($id);

        $price = $food->price * $this->form->getState()['quantity'];

        return $price;
    }
}
