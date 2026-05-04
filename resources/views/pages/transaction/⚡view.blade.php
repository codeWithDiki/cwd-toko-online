<?php

use App\Models\Transaction;
use Livewire\Component;

new class extends Component
{
    public Transaction $transaction;


    public function mount(Transaction $transaction)
    {
        $this->transaction = $transaction;
    }
};
?>
@section("title", "Detail Transaksi {$transaction->trx_id} - {$siteSettings->site_name}")
<div class="mx-auto container py-9 space-y-6 px-2">
    <div>
        <h1 class="text-2xl font-bold text-center">
            Detail Transaksi
        </h1>
        <span class="block text-center text-gray-500 text-xs">
            {{ $transaction->trx_id }}
        </span>
        <span class="block rounded-full px-3 text-xs py-1 text-white font-bold text-center bg-blue-500/80 w-max mx-auto">
            {{ ucfirst($transaction->status->value) }}
        </span>
    </div>
    <hr class="border-gray-300">
    <div class="rounded-md px-3 py-12 bg-[#B6B6B6]/20 flex flex-col gap-6 items-center justify-center">
        <div class="bg-white rounded-md p-3 w-full max-w-md">
            <h2 class="text-lg font-semibold mb-3">
                Detail Item Transaksi
            </h2>
            <div class="flex flex-col gap-4">
                @foreach($transaction?->items as $item)
                <div class="flex items-center gap-3">
                    <div class="w-16 h-16 bg-gray-200 rounded-md overflow-hidden">
                        <img src="{{ $item->itemable?->primary_image_url ?? $item->itemable?->product?->primary_image_url }}" alt="{{ $item->name }}" class="w-full h-full object-cover">
                    </div>
                    <div class="flex-1">
                        <h4 class="font-semibold">{{ $item->name }}</h4>
                        <p class="text-sm text-gray-500">Quantity: {{ $item->quantity }}</p>
                        <p class="text-sm text-gray-500">Harga : Rp {{ number_format($item->price, 0, ",", ".") }} </p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        <div class="bg-white rounded-md p-3 w-full max-w-md">
            <h2 class="text-lg font-semibold mb-3">
                Track Status Transaksi
            </h2>
            <div class="flex flex-col gap-4">
                <ul role="list" class="space-y-6">
                    @forelse($transaction->logs()->latest()->get() as $log)
                        @if($loop->last)
                            <li class="relative flex gap-x-4">
                                <div class="absolute top-0 left-0 flex h-6 w-6 justify-center">
                                    <div class="w-px bg-gray-200"></div>
                                </div>
                                <div class="relative flex size-6 flex-none items-center justify-center bg-white">
                                    <div class="size-1.5 rounded-full bg-gray-100 ring ring-gray-300"></div>
                                </div>
                                <div class="flex-auto py-0.5 text-xs/5 text-gray-500 space-y-3">
                                    <div>
                                        <span class="font-medium text-gray-900">Pergantian Status</span> dari <span class="font-bold">{{ $log->from_status }}</span> ke {{ $log->to_status }}.
                                    </div>
                                    @if($log->note)
                                        <div class="p-3 bg-gray-100 rounded-md">
                                            <p class="text-sm text-gray-700">{{ $log->note }}</p>
                                        </div>
                                    @endif
                                </div>
                                <time datetime="2023-01-24T09:20" class="flex-none py-0.5 text-xs/5 text-gray-500">{{ $log->created_at->diffForHumans(now()) }}</time>
                            </li>
                        @else
                            <li class="relative flex gap-x-4">
                                <div class="absolute top-0 -bottom-6 left-0 flex w-6 justify-center">
                                    <div class="w-px bg-gray-200"></div>
                                </div>
                                <div class="relative flex size-6 flex-none items-center justify-center bg-white">
                                    <div class="size-1.5 rounded-full bg-gray-100 ring ring-gray-300"></div>
                                </div>
                                <div class="flex-auto py-0.5 text-xs/5 text-gray-500 space-y-3">
                                    <div>
                                        <span class="font-medium text-gray-900">Pergantian Status</span> dari <span class="font-bold">{{ $log->from_status }}</span> ke {{ $log->to_status }}.
                                    </div>
                                    @if($log->note)
                                        <div class="p-3 bg-gray-100 rounded-md">
                                            <p class="text-sm text-gray-700">{{ $log->note }}</p>
                                        </div>
                                    @endif
                                </div>
                                <time datetime="2023-01-23T10:32" class="flex-none py-0.5 text-xs/5 text-gray-500">{{ $log->created_at->diffForHumans(now()) }}</time>
                            </li>
                        @endif
                    @empty
                        <p class="text-sm text-gray-500">Belum ada log untuk transaksi ini.</p>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</div>