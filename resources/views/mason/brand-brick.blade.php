@props([
    'brands' => null
])

<div class="py-5 px-4 bg-white">
    <div class="flex gap-9 items-center justify-center">
        @foreach ($brands as $brand)
            <figure class="w-24 h-auto relative">
                <img src="{{ $brand['thumbnail_url'] }}" alt="{{ $brand['name'] }}" class="w-full h-full object-contain rounded-md">
            </figure>
        @endforeach
    </div>
</div>
