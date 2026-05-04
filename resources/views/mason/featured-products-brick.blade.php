@props([
    "title" => null,
    "description" => null,
    "products" => []
])
<div class="space-y-6 py-6 md:py-12 lg:py-24 container mx-auto w-full px-2">
    <div class="space-y-3 mx-auto text-center">
        <h3 class="text-4xl font-bold">
            {{ $title }}
        </h3>
        <p class="text-sm text-gray-500">
            {{ $description }}
        </p>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3 md:gap-6 lg:gap-12">
        @foreach ($products as $product)
            <x-product-card :product="$product" display="maximal" :show_add_to_cart="false" />
        @endforeach
    </div>
</div>
