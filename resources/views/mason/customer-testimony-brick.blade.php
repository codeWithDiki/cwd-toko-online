@props([
    "title" => null,
    "description" => null,
    "testimonies" => []
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
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach ($testimonies as $testimony)
            <div class="bg-white p-6 rounded-lg shadow-md">
                <p class="text-gray-700 mb-4">"{{ $testimony['testimony'] }}"</p>
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 relative rounded-full bg-gray-300 flex items-center justify-center text-white font-bold">
                        <img src="{{ $testimony['customer_photo'] }}" alt="{{ $testimony['customer_name'] }}" class="w-full h-full object-cover rounded-full">
                    </div>
                    <div>
                        <p class="font-semibold">{{ $testimony['customer_name'] }}</p>
                        <p class="text-sm text-gray-500">{{ $testimony['customer_title'] }}</p>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
