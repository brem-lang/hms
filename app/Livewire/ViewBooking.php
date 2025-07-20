<?php

namespace App\Livewire;

use App\Filament\Resources\BookingResource;
use App\Models\Booking;
use App\Models\User;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;
use Livewire\Component;

class ViewBooking extends Component implements HasForms
{
    use InteractsWithForms;

    public Booking $booking;

    public ?array $formData = [];

    public function render()
    {
        return view('livewire.view-booking');
    }

    public function mount($id)
    {
        $this->booking = Booking::with('user', 'room', 'suiteRoom', 'walkingGuest')->find($id);

        $this->authorize('view', $this->booking);

        $this->form->fill([
            'proof_of_payment' => $this->booking->proof_of_payment,
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
                    ->hint('You can pay 50% || Please upload the proof of payment for gcash.'),
            ])
            ->statePath('formData');
    }

    public function pay()
    {
        $data = $this->form->getState();

        $this->booking->proof_of_payment = $data['proof_of_payment'];

        $this->booking->is_proof_send = true;

        $this->booking->save();

        $this->dispatch('swal:success', [
            'title' => 'Error',
            'icon' => 'danger',
        ]);

        Notification::make()
            ->success()
            ->title('Payment Sent')
            ->icon('heroicon-o-check-circle')
            ->body(auth()->user()->name.' has sent payment')
            ->actions([
                Action::make('view')
                    ->label('View')
                    ->url(fn () => BookingResource::getUrl('view', ['record' => $this->booking->id]))
                    ->markAsRead(),
            ])
            ->sendToDatabase(User::where('role', '!=', 'customer')->get());

        redirect('/view-booking/'.$this->booking->id);
    }
}
