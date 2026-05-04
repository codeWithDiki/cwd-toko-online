<?php

use App\Models\Cart;
use App\Models\CartItem;
use CodeWithDiki\ProductModule\Models\Product;
use CodeWithDiki\ProductModule\Models\ProductVariant;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use Livewire\Component;

new class extends Component
{
    public ?Cart $cart = null;

    public bool $openModal = false;

    public function mount()
    {
        if(Auth::check()) {
            if(!Auth::user()->cart) {
                $cart = Cart::create([
                    "user_id" => Auth::id(),
                ]);
                $this->cart = $cart;
            } else {
                $this->cart = Auth::user()->cart;
            }
            $this->cart = Auth::user()->cart;
        }
    }

    #[On("addToCart")]
    public function addToCart(array $data)
    {

        try {
            $productId = $data["product"];
            $product = $data['is_variant'] ? ProductVariant::find($productId) : Product::find($productId);
            $quantity = $data["quantity"] ?? 1;

            if(Auth::guest()) {
                throw new \Exception("You must be logged in to add items to cart.");
            }

            if($quantity < 1) {
                throw new \Exception("Quantity must be at least 1.");
            }

            if(!$product){
                throw new \Exception("Product not found.");
            }

            $cart = $this->cart;

            $cartItem = $cart->items()->where([
                "cartitemable_id" => $product->id,
                "cartitemable_type" => get_class($product),
            ])->first();

            if($cartItem) {
                $cartItem->quantity += $quantity;
                $cartItem->save();

                Notification::make()
                    ->title("Produk ditambahkan ke keranjang")
                    ->body("Produk " . $product->name . " berhasil ditambahkan ke keranjang.")
                    ->success()
                    ->actions([
                        Action::make("view_cart")
                            ->label("Lihat Keranjang")
                            ->dispatch('openCartModal'),
                    ])
                    ->send();

                return;
            }

            $cart->items()->create([
                "cartitemable_id" => $product->id,
                "cartitemable_type" => get_class($product),
                "quantity" => $quantity,
            ]);

            Notification::make()
                ->title("Produk ditambahkan ke keranjang")
                ->body("Produk " . $product->name . " berhasil ditambahkan ke keranjang.")
                ->success()
                ->actions([
                    Action::make("view_cart")
                        ->label("Lihat Keranjang")
                        ->dispatch('openCartModal'),
                ])
                ->send();

        } catch (\Exception $e) {
            $this->dispatch("cartError", ["message" => $e->getMessage()]);
        }

    }

    #[On("cartUpdated")]
    public function cartUpdated()
    {
        $this->cart->refresh();
    }

    #[On("openCartModal")]
    public function openCartModal()
    {
        $this->openModal = true;
    }

};
?>

<div x-data="{ openModal: $wire.entangle('openModal') }" class="relative">
    <div class="relative" @click="openModal = !openModal">
        @svg("heroicon-o-shopping-cart", "w-5 h-5 text-gray-500")
        <span class="text-xs absolute -top-3 -right-3 bg-red-600 text-white rounded-full w-5 h-5 flex items-center justify-center font-bold">
            {{ $cart ? $cart->items()->count() : 0 }}
        </span>
    </div>
    <!-- create a dropdown for cart items -->
    <div class="fixed inset-0 z-100 bg-gray-900/50 px-2 flex flex-col justify-center items-center" 
    style="display: none;"
    x-show="openModal">
        <div class="w-full max-w-md bg-white rounded-md p-4" 
        @click.away="openModal = false"
        x-show="openModal" 
        x-transition>
            <div class="flex justify-between mb-4 items-center">
                <h3 class="text-lg font-semibold">Keranjang</h3>
                <button @click="openModal = false" class="text-gray-500 hover:text-gray-700">
                    @svg("heroicon-o-x-mark", "w-5 h-5")
                </button>
            </div>
            <div class="space-y-4 max-h-[300px] overflow-y-auto">
                @if($cart && $cart->items()->count() > 0)
                    @foreach ($cart->items as $item)
                        <div class="flex items-center gap-3">
                            <div class="w-16 h-16 bg-gray-200 rounded-md overflow-hidden">
                                <img src="{{ $item->cartitemable?->product?->primary_image_url ?? $item->cartitemable?->primary_image_url }}" alt="{{ $item->cartitemable->name }}" class="w-full h-full object-cover">
                            </div>
                            <div class="flex-1">
                                <h4 class="font-semibold">{{ $item->cartitemable?->name }} @if(!empty($item->cartitemable->product)) - {{ $item->cartitemable?->product?->name }} @endif</h4>
                                <p class="text-sm text-gray-500">Quantity: {{ $item->quantity }}</p>
                                <p class="text-sm text-gray-500">Harga : Rp {{ number_format($item->cartitemable->discount_price ?? $item->cartitemable->price, 0, ",", ".") }} </p>
                            </div>
                        </div>
                    @endforeach
                @else
                    <p class="text-gray-500">Keranjang kamu masih kosong..</p>
                @endif
            </div>
            @if($cart && $cart->items()->count() > 0)
            <div class="flex items-center justify-between gap-3 pt-4">
                <div>
                    <h4 class="font-semibold">Total:</h4>
                    <p class="text-sm text-gray-500">Rp {{ number_format($cart->items->sum(function($item) {
                        return ($item->cartitemable->discount_price ?? $item->cartitemable->price) * $item->quantity;
                    }), 0, ",", ".") }}</p>
                </div>
                <div class="flex gap-3 items-center">
                    <a href="{{ route('cart.checkout') }}" wire:navigate class="block px-4 py-2 bg-black text-white rounded-md">
                        Checkout
                    </a>
                </div>
            </div>
            @endif
        </div>    
    </div>
</div>