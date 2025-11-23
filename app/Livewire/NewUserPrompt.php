<?php

namespace App\Livewire;

use Livewire\Component;

class NewUserPrompt extends Component
{
    public function render()
    {
        return view('livewire.new-user-prompt');
    }

    public function continue()
    {
        auth()->user()->update([
            'is_new_user' => false,
        ]);

        return redirect()->route('index');
    }
}
