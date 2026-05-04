<?php

use App\Models\Cart;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

new class extends Component
{
    public Cart $cart;

    public function mount()
    {
        $this->cart = Auth::user()->cart;
    }

};
?>

<div>
    {{-- It is quality rather than quantity that matters. - Lucius Annaeus Seneca --}}
</div>