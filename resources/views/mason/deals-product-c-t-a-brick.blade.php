@props([
    "title" => null,
    "description" => null,
    "deal_end_date" => null,
    "products" => []
])

<div class="py-12 bg-[#B6B6B6]/20">
    <div class="md:container mx-auto w-full px-2">
        <div class="flex flex-col md:flex-row gap-8">
            <div class="flex-1 text-center md:text-left">
                <h2 class="text-3xl font-bold mb-4">{{ $title }}</h2>
                <p class="text-gray-700 mb-6">{{ $description }}</p>
                <div class="space-y-3">
                    <h4 class="text-lg font-semibold">
                        Hurry, Before It's Too Late! 
                    </h4>
                    <div class="grid grid-cols-4 font-ds-digital gap-3 md:gap-6">
                        <div class="flex flex-col gap-2 items-center justify-center">
                            <div class="bg-white shadow-lg w-full rounded-md min-h-20 flex flex-col items-center justify-center p-4">
                                <div class="text-2xl md:text-4xl lg:text-6xl">
                                    {{ round(now()->diffInDays(\Carbon\Carbon::parse($deal_end_date))) }}
                                </div>
                            </div>
                            <div>
                                <span class="text-sm text-gray-500">Days</span>
                            </div>
                        </div>
                        <div class="flex flex-col gap-2 items-center justify-center">
                            <div class="bg-white shadow-lg w-full rounded-md min-h-20 flex flex-col items-center justify-center p-4">
                                <div class="text-2xl md:text-4xl lg:text-6xl">
                                    {{ round(now()->diff(\Carbon\Carbon::parse($deal_end_date))->format("%h")) }}
                                </div>
                            </div>
                            <div>
                                <span class="text-sm text-gray-500">Hours</span>
                            </div>
                        </div>
                        <div class="flex flex-col gap-2 items-center justify-center">
                            <div class="bg-white shadow-lg w-full rounded-md min-h-20 flex flex-col items-center justify-center p-4">
                                <div class="text-2xl md:text-4xl lg:text-6xl">
                                    {{ round(now()->diff(\Carbon\Carbon::parse($deal_end_date))->format("%i")) }}
                                </div>
                            </div>
                            <div>
                                <span class="text-sm text-gray-500">Minutes</span>
                            </div>
                        </div>
                        <div class="flex flex-col gap-2 items-center justify-center">
                            <div class="bg-white shadow-lg w-full rounded-md min-h-20 flex flex-col items-center justify-center p-4">
                                <div class="text-2xl md:text-4xl lg:text-6xl">
                                    {{ round(now()->diff(\Carbon\Carbon::parse($deal_end_date))->format("%s")) }}
                                </div>
                            </div>
                            <div>
                                <span class="text-sm text-gray-500">Seconds</span>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <div class="flex-1 flex flex-nowrap overflow-x-auto gap-6 p-3">
                @foreach ($products as $product)
                    <x-product-card 
                        :product="$product"
                        :show_add_to_cart="false"
                    />
                @endforeach
            </div>
        </div>
    </div>
    
</div>
