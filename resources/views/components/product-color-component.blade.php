@props([
'product',
])

<div class="space-y-2">
    <h3 class="text-sm font-bold">
        Warna : {{ $this->selected_color?->label ?? "N/A" }}
    </h3>
    <div class="flex flex-wrap gap-1.5">
        @foreach ($product->colors as $color)
        <div
            wire:click="selectColor({{ $color }})"
            class="w-6 h-6 rounded-full cursor-pointer @if($color == $this->selected_color) border-3 border-gray-300 outline-2 outline-blue-500 @endif"
            style="background-color: {{ $color->color_hex }};">
        </div>
        @endforeach
    </div>
</div>