@props([
'product',
])

<div class="space-y-2">
    <h3 class="text-sm font-bold">
        Ukuran : {{ $this->selected_size?->label ?? "N/A" }}
    </h3>
    <div class="flex flex-wrap gap-1.5">
        @foreach ($product->sizes as $size)
        <div
            wire:click="selectSize({{ $size }})"
            class="px-3 py-1 text-sm rounded-sm cursor-pointer border border-gray-300 @if($this->selected_size == $size) bg-black text-white @endif">
            {{ $size->label }}
        </div>
        @endforeach
    </div>
</div>