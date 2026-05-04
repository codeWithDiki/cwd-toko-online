@props([
"product" => null,
"display" => "minimal",
"show_add_to_cart" => false,
])
<div class="bg-white shrink-0 p-4 @if($display == 'minimal') w-64 @else w-full @endif rounded-lg shadow-md space-y-3">
    <figure class="w-full h-48 relative mb-4">
        <img src="{{ $product->primary_image_url }}" alt="{{ $product->name }}" class="w-full h-full object-cover rounded-md">
    </figure>
    <h3 class="text-lg font-semibold mb-2">{{ $product->name }}</h3>
    <div>
        <p class="text-gray-700 @if($product->discount_price) line-through text-sm text-red-500 @endif">
            Rp{{ number_format($product->price, 0) }}
        </p>
        @if($product->discount_price)
            <p class="text-gray-700 mb-4">Rp{{ number_format($product->discount_price, 0) }}</p>
        @endif
    </div>
    <p class="text-xs text-gray-600">
        {{ $product->description }}
    </p>
    <div class="flex items-center gap-2">
        <a href="{{ route('product.view', ['product' => $product]) }}" wire:navigate class="text-sm text-blue-700 hover:text-blue-900">
            Lihat Produk
        </a>
    </div>
</div>