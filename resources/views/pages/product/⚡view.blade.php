<?php

use App\Models\Product;
use CodeWithDiki\PaymentModule\Facades\PaymentModule;
use CodeWithDiki\ProductModule\Models\ProductColor;
use CodeWithDiki\ProductModule\Models\ProductSize;
use CodeWithDiki\ProductModule\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;

new class extends Component
{
    public Product $product;

    public ?ProductVariant $selected_variant = null;

    public int $quantity = 1;

    public ?int $review_rating = 5;

    public function mount(Request $request, Product $product)
    {
        $this->product = $product;
    }

    public function increaseQuantity()
    {
        $this->quantity++;
    }

    public function decreaseQuantity()
    {
        if($this->quantity > 1) {
            $this->quantity--;
        }
    }

    public function selectVariant(?ProductVariant $variant = null)
    {
        $this->selected_variant = $variant;
    }

    public function getPaymentGroup() : Collection
    {
        return PaymentModule::getActivePaymentMethodGroups()->map(function($group){
            $group->image_url = Storage::url($group->image_url);

            return $group;
        });
    }

    public function getReviews() : Collection
    {
        return $this->product
        ->reviews()
        ->when($this->review_rating, function($query) {
            $query->where('rating', $this->review_rating);
        })
        ->latest()->get();
    }

    public function addToCart()
    {
        $productToAdd = $this->selected_variant->id ?? $this->product->id;

        $this->dispatch("addToCart", [
            "product" => $productToAdd,
            "quantity" => $this->quantity,
            "is_variant" => $this->selected_variant ? true : false,
        ]);
    }

};
?>
@section("title", "{$product->name} - {$siteSettings->site_name}")
<div class="container mx-auto py-6 px-2 space-y-9">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-3 lg:gap-12">
        <div class="flex gap-3 items-start" x-data="imagesUtility()">
            <div class="flex flex-col gap-3">
                <template x-for="(image, index) in images" :key="index">
                    <img 
                        :src="image" 
                        alt="{{ $product->name }} Image " 
                        class="w-16 h-16 object-cover rounded cursor-pointer border-2" 
                        :class="selectedImageUrl === image ? 'border-blue-500' : 'border-transparent'"
                        @click="selectImage(image)"
                    >
                </template>
            </div>
            <div class="flex-1">
                <img :src="selectedImageUrl" src="{{ $product->primary_image_url }}" alt="{{ $product->name }}" class="w-full h-auto object-cover rounded">
            </div>
        </div>
        <div class="space-y-6">
            <div>
                <span class="block font-semibold text-xs text-gray-500">
                    {{ $siteSettings->site_name }}
                </span>
                <h1 class="text-lg font-bold">
                    {{ $product->name }}
                </h1>
                <p class="text-xs text-gray-400">
                    {{ $product->description }}
                </p>
                <div aria-details="rating">
                    @for ($i = 0; $i < 5; $i++)
                        @if ($i < floor(($product->reviews?->avg("rating") ?? 0)))
                            @svg("heroicon-s-star", "w-4 h-4 text-yellow-400 inline")
                        @else
                            @svg("heroicon-o-star", "w-4 h-4 text-gray-300 inline")
                        @endif
                    @endfor
                    <span class="text-sm text-gray-500">
                        ({{ number_format($product->reviews->avg("rating"), 1) }})
                    </span>
                </div>
            </div>
            <div>
                <div class="flex gap-3 items-center">
                    <p class="text-gray-700 font-bold">
                        Rp{{ number_format($selected_variant->discount_price ?? $selected_variant->price ?? $product->discount_price ?? $product->price, 0) }}
                    </p>
                    @if($product->discount_price || $selected_variant?->discount_price)
                        <p class="text-gray-400 line-through text-xs">Rp{{ number_format($selected_variant->price ?? $product->price, 0) }}</p>
                        <div class="rounded-full bg-[#DA3F3F] px-3 py-1 text-xs text-white font-semibold">
                            Menghemat {{ round(((($selected_variant->price ?? $product->price) - ($selected_variant->discount_price ?? $product->discount_price)) / ($selected_variant->price ?? $product->price)) * 100) }}%
                        </div>
                    @endif
                </div>
            </div>
            <div class="space-y-1">
                <h3 class="text-xs text-gray-400">
                    Tersedia {{ $product->stock }} produk lagi!
                </h3>
                <div class="rounded-full h-2 bg-gray-300 relative">
                    <div class="
                    @if($product->stock >= 100)
                        bg-green-500
                        w-full
                    @elseif($product->stock <= 100 && $product->stock >= 50) 
                        bg-amber-300 
                        w-[75%]
                    @elseif($product->stock <= 50 && $product->stock >= 10)
                        bg-red-500
                        w-[25%]                
                    @endif h-2 rounded-full">
                    </div>
                </div>
            </div>
            @if($product->variants->isNotEmpty())
                <div class="space-y-2">
                    <h3 class="text-sm font-bold">
                        Varian : {{ $this->selected_variant?->name ?? "N/A" }}
                    </h3>
                    <div class="flex flex-wrap gap-3">
                        <div class="cursor-pointer px-3 py-1 text-sm rounded-md @if(empty($this->selected_variant)) bg-black text-white @else bg-gray-200 text-gray-700 @endif" wire:click="selectVariant()">
                            Original
                        </div>
                        @foreach ($product->variants as $variant)
                            <div class="cursor-pointer px-3 py-1 text-sm rounded-md @if($variant == $this->selected_variant) bg-black text-white @else bg-gray-200 text-gray-700 @endif" wire:click="selectVariant({{ $variant }})">
                                {{ $variant->name }}
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
            <div class="flex items-center gap-6">
                <div class="shrink-0 grid grid-cols-3 py-1 items-center rounded-md border border-gray-300">
                    <button class="px-2 py-1 text-sm" wire:click="decreaseQuantity()">
                        -
                    </button>
                    <div class="px-2 py-1 text-sm text-center">
                        <span>{{ $quantity }}</span>
                    </div>
                    <button class="px-2 py-1 text-sm" wire:click="increaseQuantity()">
                        +
                    </button>
                </div>
                <button wire:click="addToCart()" type="button" class="w-full py-2 border border-gray-300 rounded-md text-sm font-semibold cursor-pointer">
                    Add to Cart
                </button>
            </div>
            @if($this->getPaymentGroup()->isNotEmpty())
                <div class="py-3">
                    <h3>
                        Menerima Pembayaran melalui :
                    </h3>
                    <div class="flex flex-wrap gap-3 mt-2">
                        @foreach ($this->getPaymentGroup() as $group )
                            <div class="px-6 py-3 border border-gray-300 rounded-md flex flex-col gap-2 items-center gap-2">
                                <img src="{{ $group->image_url }}" alt="" class="h-12 w-12 rounded-md object-cover">
                                <h4 class="text-xs text-gray-500">
                                    {{ $group->name }}
                                </h4>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
    <div class="bg-[#B6B6B6]/20 py-12 px-2 rounded-lg space-y-3">
        <div class="">
            <h2 class="text-2xl text-black">
                Review Pelanggan
            </h2>
        </div>
        <hr class="border-gray-300">
        @if($this->getReviews()->isEmpty())
            <div class="py-6">
                <p class="text-center text-gray-500">
                    Belum ada review untuk produk ini.
                </p>
            </div>
        @else
            <div class="grid grid-cols-2 gap-3">
                @foreach ($this->getReviews() as $review)
                    <div class="border border-gray-300 rounded-md p-4 bg-white">
                        <div class="flex items-center gap-3 mb-2">
                            <div class="w-10 h-10 rounded-full bg-gray-300 flex items-center justify-center text-sm text-white">
                                {{ strtoupper(substr($review->from, 0, 1)) }}
                            </div>
                            <div class="space-y-6 w-full">
                                <div>
                                    <h4 class="text-sm font-bold">
                                        {{ $review->from }}
                                    </h4>
                                    <div aria-details="rating">
                                        @for ($i = 0; $i < 5; $i++)
                                            @if ($i < floor($review->rating))
                                                @svg("heroicon-s-star", "w-4 h-4 text-yellow-400 inline")
                                            @else
                                                @svg("heroicon-o-star", "w-4 h-4 text-gray-300 inline")
                                            @endif
                                        @endfor
                                    </div>
                                </div>
                                @if($review->message)
                                    <div class="bg-gray-200 w-full p-3 rounded-md">
                                        <p class="text-xs text-gray-500">
                                            {{ $review->message }}
                                        </p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
    <script>
        function imagesUtility()
        {
            return {
                selectedImageUrl: "{{ $product->primary_image_url }}",
                images : @json($product->images->map(function($image) {
                    return \Illuminate\Support\Facades\Storage::url($image->image_url);
                })),
                selectImage(imageUrl) {
                    this.selectedImageUrl = imageUrl;
                }
            };
        }
    </script>
</div>