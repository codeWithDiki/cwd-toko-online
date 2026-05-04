<?php

use App\Models\Cart;
use CodeWithDiki\PaymentModule\Data\PaymentData;
use CodeWithDiki\PaymentModule\Enums\PaymentVendor;
use CodeWithDiki\PaymentModule\Facades\PaymentModule;
use CodeWithDiki\TransactionModule\Data\CustomerData;
use CodeWithDiki\TransactionModule\Data\TransactionData;
use CodeWithDiki\TransactionModule\Data\TransactionItemData;
use CodeWithDiki\TransactionModule\Enums\PaymentStatus;
use CodeWithDiki\TransactionModule\Enums\TransactionStatus;
use CodeWithDiki\TransactionModule\Facades\TransactionModule;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\On;
use Livewire\Component;

new class extends Component
{
    public Cart $cart;

    public ?string $customer_name = null;
    public ?string $customer_email = null;
    public ?string $customer_phone = null;
    public ?string $customer_address = null;
    public ?int $payment_method_id = null; 
    public ?string $notes = null;
    public ?string $payment_code = null;
    public bool $isProcessingPayment = false;

    public function mount()
    {
        $this->cart = Auth::user()->cart;
    }

    public function getItems() : Collection
    {
        return $this->cart->items->map(function($item){
            $is_variant = $item->cartitemable_type === \CodeWithDiki\ProductModule\Models\ProductVariant::class;
        
            return [
                "item_id" => $item->id,
                "name" => $is_variant ? $item->cartitemable->product->name . " - " . $item->cartitemable->name : $item->cartitemable->name,
                "quantity" => $item->quantity,
                "price" => ($item->cartitemable->discount_price ?? $item->cartitemable->price),
                "image" => $is_variant ? $item->cartitemable->product->primary_image_url : $item->cartitemable->primary_image_url,
                "total_price" => ($item->cartitemable->discount_price ?? $item->cartitemable->price) * $item->quantity,
            ];
        });
    }

    public function increaseQuantity($itemId)
    {
        $item = $this->cart->items()->findOrFail($itemId);
        if($item) {
            $item->quantity += 1;
            $item->save();
            $this->cart->refresh();
        }
        $this->dispatchCartRefreshEvent();
    }

    public function decreaseQuantity($itemId)
    {
        $item = $this->cart->items()->findOrFail($itemId);
        if($item && $item->quantity > 1) {
            $item->quantity -= 1;
            $item->save();
            $this->cart->refresh();
        }
        $this->dispatchCartRefreshEvent();
    }

    public function removeItem($itemId)
    {
        $item = $this->cart->items()->findOrFail($itemId);
        if($item) {
            $item->delete();
            $this->cart->refresh();
        }
        $this->dispatchCartRefreshEvent();

        if($this->cart->items()->count() == 0) {
            return redirect()->route("home");
        }
    }

    private function dispatchCartRefreshEvent()
    {
        $this->dispatch("cartUpdated");
    }

    public function checkout()
    {
        // Validate input
        $this->validate([
            "customer_name" => "required|string|max:255",
            "customer_email" => "required|email|max:255",
            "customer_phone" => "required|string|max:20",
            "customer_address" => "required|string|max:500",
            "payment_method_id" => "required|exists:payment_methods,id",
            "notes" => "nullable|string|max:1000",
        ]);

        $payment_code = "PAY-" . now()->format("YmdHis") . rand(1000, 9999);
        $this->payment_code = $payment_code;

        $customer_data = new CustomerData(
            $this->customer_name,
            $this->customer_email,
            $this->customer_phone,
            $this->customer_address,
        );

        $customer = TransactionModule::createCustomer($customer_data);

        $transaction_item = collect([]);

        $this->cart->items->each(function($item) use (&$transaction_item) {
            $is_variant = $item->cartitemable_type === \CodeWithDiki\ProductModule\Models\ProductVariant::class;
            $transaction_item->push(new TransactionItemData(
                itemable:$item->cartitemable,
                name:$is_variant ? $item->cartitemable->product->name . " - " . $item->cartitemable->name : $item->cartitemable->name,
                description: $is_variant ? $item->cartitemable->product->description : $item->cartitemable->description,
                price: ($item->cartitemable->discount_price ?? $item->cartitemable->price),
                quantity: $item->quantity,
                total: ($item->cartitemable->discount_price ?? $item->cartitemable->price) * $item->quantity,
            ));
        });

        $transaction_data = new TransactionData(
            trx_id: "TRX-" . now()->format("YmdHis") . rand(1000, 9999),
            customer_id: $customer->id,
            total_amount: $transaction_item->sum("total"),
            payment_status:PaymentStatus::PENDING,
            status:TransactionStatus::ONHOLD,
            notes: $this->notes,
        );


        $transaction = TransactionModule::createTransaction($transaction_data, $transaction_item);

        $payment_data = new PaymentData(
            paymentable:$transaction,
            payment_method_id: $this->payment_method_id,
            amount: $transaction->total_amount,
            payment_code: $payment_code,
            status: \CodeWithDiki\PaymentModule\Enums\PaymentStatus::PENDING,
            customer_name: $this->customer_name,
            customer_email: $this->customer_email,
            customer_phone: $this->customer_phone,
            customer_address: $this->customer_address,
        );

        $payment = PaymentModule::createPayment($payment_data);

        $payment->users()->attach(Auth::id());
        $transaction->users()->attach(Auth::id());

        $this->dispatch("paymentInitiated", [
            "payment_code" => $payment_code,
        ]);

        $this->isProcessingPayment = true;
        
    }

    public function getPaymentMethods() : Collection
    {
        return PaymentModule::getActivePaymentMethodGroups();
    }

    public function setPaymentMethod(int $methodId)
    {
        $method = PaymentModule::getPaymentMethodById($methodId);
        if($method) {
            $this->payment_method_id = $methodId;
        } else {
            $this->addError("payment_method_id", "Selected payment method is invalid.");
        }
    }

    #[On("echo-private:payment-gateway-processed,.cwd.payment-module.payment-gateway-processed")]
    public function handlePaymentProcessed($event)
    {
        $event = collect($event);

        $payment = PaymentModule::getPaymentByCode($event['payment']['payment_code']);

        if($payment->paymentMethod->vendor == PaymentVendor::Midtrans && $payment->payment_response['status_code'] == 201) {
            
            // clear cart
            $cart = $this->cart;
            $cart->items()->delete();

        }

        return redirect()->route("payment.view", $payment);
    }

};
?>
@section("title", "Checkout - {$siteSettings->site_name}")
<div class="container mx-auto py-12 space-y-6 px-2">
    <div class="text-center">
        <h1 class="text-3xl font-semibold">
            Checkout Keranjang
        </h1>
    </div>
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div>
            @forelse ($this->getItems() as $item)
                <div class="space-y-3 py-4 border-b border-gray-200">
                    <div class="flex items-center gap-4">
                        <div class="w-16 h-16 bg-gray-200 rounded-md overflow-hidden">
                            <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}" class="w-full h-full object-cover">
                        </div>
                        <div class="flex-1">
                            <h2 class="font-medium">{{ $item['name'] }}</h2>
                            <p class="text-sm text-gray-500">Jumlah: {{ $item['quantity']}}</p>
                        </div>
                        <p class="font-semibold">Rp {{ number_format($item['price'], 0, ",", ".") }}</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="shrink-0 grid grid-cols-3 py-1 items-center rounded-md border border-gray-300">
                            <button class="px-2 py-1 text-sm" wire:click="decreaseQuantity({{ $item['item_id'] }})">
                                -
                            </button>
                            <div class="px-2 py-1 text-sm text-center">
                                <span>{{ $item['quantity'] }}</span>
                            </div>
                            <button class="px-2 py-1 text-sm" wire:click="increaseQuantity({{ $item['item_id'] }})">
                                +
                            </button>
                        </div>
                        <button class="text-red-500 border border-red-500 px-3 py-2 rounded-md text-sm" wire:click="removeItem({{ $item['item_id'] }})">
                            @svg("heroicon-o-trash", "w-5 h-5")
                        </button>
                    </div>
                </div>
            @empty
                <p class="text-gray-500 text-center py-12">Keranjang kamu masih kosong..</p>
            @endforelse
            <div class="flex justify-end mt-6 pb-6 border-b border-gray-200">
                <p class="text-lg font-semibold">Total: Rp {{ number_format($this->getItems()->sum(function($item){
                    return $item['total_price'];
                }), 0, ",", ".") }}</p>
            </div>
        </div>
        <form wire:submit.prevent="checkout" class="py-2 px-6 rounded-lg border border-gray-300">
            <div class="space-y-4 mt-6">
                <h3 class="text-xl font-sebibold">
                    Informasi Customer
                </h3>
                <div class="space-y-2">
                    <label for="customer_name" class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                    <input type="text" id="customer_name" wire:model.defer="customer_name" class="py-2 block w-full px-3 rounded-md border border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                    @error("customer_name") <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                <div class="space-y-2">
                    <label for="customer_email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" id="customer_email" wire:model.defer="customer_email" class="py-2 block w-full px-3 rounded-md border border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                    @error("customer_email") <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                <div class="space-y-2">
                    <label for="customer_phone" class="block text-sm font-medium text-gray-700">No. Telepon</label>
                    <input type="text" id="customer_phone" wire:model.defer="customer_phone" class="py-2 block w-full px-3 rounded-md border border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                    @error("customer_phone") <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                <div class="space-y-2">
                    <label for="customer_address" class="block text-sm font-medium text-gray-700">Alamat</label>
                    <textarea id="customer_address" wire:model.defer="customer_address" class="py-2 block w-full px-3 rounded-md border border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"></textarea>
                    @error("customer_address") <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                <div class="space-y-2">
                    <label for="payment_method_id" class="block text-sm font-medium text-gray-700">Metode Pembayaran</label>
                    @foreach($this->getPaymentMethods() as $group)
                        <div x-data="{open: false}">
                            <div  @click="open = !open" class="w-full border border-gray-400 text-sm text-left px-3 py-2 bg-gray-100/75 rounded-md flex items-center justify-between cursor-pointer">
                                <span>{{ $group->name }}</span>
                                @svg("heroicon-o-chevron-down", "w-4 h-4")
                            </div>
                            <div x-show="open" class="mt-1 space-y-1">
                                @foreach($group->paymentMethods as $method)
                                    <div class="border @if($payment_method_id == $method->id) border-blue-500 bg-blue-500/20 @else border-gray-200 @endif rounded-md flex items-center gap-3 px-2 cursor-pointer" 
                                    wire:click="setPaymentMethod({{ $method->id }})">
                                        <figure class="h-12 relative w-12 shrink-0">
                                            <img src="{{ \Illuminate\Support\Facades\Storage::url($method->image_url) }}" alt="" class="w-full h-full object-contain">
                                        </figure>
                                        <div class="text-sm @if($payment_method_id == $method->id) text-blue-500 @else text-gray-500 @endif">
                                            {{ $method->name }}
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                    @error("payment_method_id") <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
            </div>
            <div class="flex justify-start mt-6">
                <button wire:loading.attr="disabled" type="submit" class="px-4 py-2 bg-black text-white text-sm rounded-md hover:bg-black/80 transition duration-200">
                    <span class="block">
                        Checkout
                    </span>
                </button>
            </div>
        </form>
    </div>
    @if($isProcessingPayment)
        <div class="fixed inset-0 bg-gray-600/20 backdrop-blur-sm w-full h-full z-50 flex items-center justify-center">
            <div class="rounded-md flex items-center gap-4">
                @svg("ei-spinner-3", "w-16 h-16 animate-spin text-black/75")
            </div>
        </div>
    @endif
</div>