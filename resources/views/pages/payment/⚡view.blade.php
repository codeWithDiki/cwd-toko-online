<?php

use CodeWithDiki\PaymentModule\Facades\PaymentModule as FacadesPaymentModule;
use CodeWithDiki\PaymentModule\Models\Payment;
use CodeWithDiki\PaymentModule\PaymentModule;
use Livewire\Attributes\On;
use Livewire\Component;

new class extends Component
{
    public Payment $payment;

    public function mount(Payment $payment)
    {
        $this->payment = FacadesPaymentModule::getPaymentByCode($payment->payment_code);
    }

    public function isPaid(): bool
    {
        return $this->payment->status == \CodeWithDiki\PaymentModule\Enums\PaymentStatus::PAID;
    }

    public function isFailed(): bool
    {
        return $this->payment->status == \CodeWithDiki\PaymentModule\Enums\PaymentStatus::FAILED;
    }

    public function isQris(): bool
    {
        if($this->payment->paymentMethod->vendor != \CodeWithDiki\PaymentModule\Enums\PaymentVendor::Midtrans) {
            return false;
        }

        return $this->payment->getQrCodeUrl() != null;
    }

    public function isVirtualAccount(): bool
    {
        if($this->payment->paymentMethod->vendor != \CodeWithDiki\PaymentModule\Enums\PaymentVendor::Midtrans) {
            return false;
        }

        return $this->payment->getMidtransVirtualAccountNumber() != null;
    }

    #[On("echo-private:payment-paid,.cwd.payment-module.payment-paid")]
    public function handlePaymentPaid($event)
    {
        $this->payment->refresh();
    }


    #[On("echo-private:payment-failed,.cwd.payment-module.payment-failed")]
    public function handlePaymentFailed($event)
    {
        $this->payment->refresh();
    }

};
?>
@section("title", "Detail Pembayaran {$payment->payment_code} - {$siteSettings->site_name}")
<div class="mx-auto container py-9 space-y-6 px-2">
    <div>
        <h1 class="text-2xl font-bold text-center">
            @if($this->isPaid())
                Pembayaran Berhasil
            @elseif($this->isFailed())
                Pembayaran Gagal
            @else
                Detail Pembayaran
            @endif
        </h1>
        <span class="block text-center text-gray-500 text-xs">
            {{ $payment->payment_code }}
        </span>
        <span class="block rounded-full px-3 text-xs py-1 text-white font-bold text-center bg-blue-500/80 w-max mx-auto">
            {{ ucfirst($payment->status->value) }}
        </span>
    </div>
    <hr class="border-gray-300">
    <div class="rounded-md px-3 py-12 bg-[#B6B6B6]/20 flex flex-col gap-6 items-center justify-center">
        <div class="bg-white rounded-md p-3 w-full max-w-md">
            @if(!$this->isPaid() && !$this->isFailed())
                <div class="flex flex-col gap-3 items-center justify-center">
                    <img src="{{ \Illuminate\Support\Facades\Storage::url($payment->paymentMethod->image_url) }}" alt="{{ $payment->paymentMethod->name }}" class="w-20 h-20 object-contain">
                    <div class="flex flex-col items-center">
                        <h2 class="text-lg font-semibold">
                            {{ $payment->paymentMethod->name }}
                        </h2>
                        @if($this->isQris())
                            <div class="flex flex-col gap-2 items-center justify-center">
                                <img src="{{ $payment->getQrCodeUrl() }}" alt="QR Code" class="w-48 h-48 object-contain">
                                <span class="text-sm text-gray-500">Scan QR Code untuk melakukan pembayaran</span>
                            </div>
                        @elseif($this->isVirtualAccount())
                            <div class="flex flex-col gap-2 items-center justify-center">
                                <div class="bg-gray-100 rounded-md px-4 py-2 w-full text-center">
                                    <span class="text-lg font-mono tracking-wide">
                                        {{ $payment->getMidtransVirtualAccountNumber() }}
                                    </span>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @elseif($this->isPaid())
                <div class="flex flex-col gap-3">
                    <div class="flex flex-col gap-1 items-center">
                        <h3>
                            Pembayaran Berhasil!
                        </h3>
                    </div>
                    <div class="w-full bg-emerald-600/20 text-emerald-500 font-bold p-3 rounded-md text-center text-sm">
                        Pembayaran sudah kami terima dan sedang diproses. Terima kasih telah berbelanja di toko kami!
                    </div>
                    <a 
                    class="block w-full text-center py-2 px-4 bg-black text-white rounded-md text-sm"
                    wire:navigate
                    href="{{ route('transaction.view', ['transaction' => $payment->paymentable]) }}"> Lacak Transaksi </a>
                </div>
            @elseif($this->isFailed())
                <div class="flex flex-col gap-3">
                    <div class="flex flex-col gap-1 items-center">
                        <h3>
                            Pembayaran Gagal!
                        </h3>
                    </div>
                    <div class="w-full bg-red-600/20 text-red-500 font-bold p-3 rounded-md text-center text-sm">
                        Maaf, terjadi kesalahan saat memproses pembayaran Anda. Silakan coba lagi atau hubungi layanan pelanggan kami untuk bantuan lebih lanjut.
                    </div>
                </div>
            @endif
        </div>
        <div class="bg-white rounded-md p-3 w-full max-w-md">
            <h2 class="text-lg font-semibold mb-3">
                Detail Transaksi
            </h2>
            <div class="flex flex-col gap-2">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-500">Nomor Transaksi</span>
                    <span class="font-mono text-sm">{{ $payment->paymentable?->trx_id ?? '-' }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-500">Tanggal Pembayaran</span>
                    <span class="font-mono text-sm">{{ $payment->paid_at ? $payment->paid_at->format('d M Y, H:i') : '-' }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-500">Total Pembayaran</span>
                    <span class="font-mono text-sm">Rp {{ number_format($payment->amount, 0, ",", ".") }}</span>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-md p-3 w-full max-w-md">
            <h2 class="text-lg font-semibold mb-3">
                Detail Item Transaksi
            </h2>
            <div class="flex flex-col gap-4">
                @foreach($payment->paymentable?->items as $item)
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
    </div>
</div>