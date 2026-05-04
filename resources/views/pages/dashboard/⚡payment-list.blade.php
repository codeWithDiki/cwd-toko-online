<?php

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use CodeWithDiki\PaymentModule\Facades\PaymentModule;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Livewire\Attributes\Layout;

new #[Layout("layouts.dashboard")] class extends Component
{
    public User $user;

    public function mount()
    {
        $this->user = Auth::user();
    }

    public function getPendingPayments() : LengthAwarePaginator
    {
        return \App\Models\Payment::query()
            ->whereHas("users", function($query) {
                $query->where("user_id", $this->user->id);
            })
            ->where("status", \CodeWithDiki\PaymentModule\Enums\PaymentStatus::PENDING)
            ->latest()
            ->paginate(5);
    }
};
?>
@section("title", "Pembayaran Pending - {$siteSettings->site_name}")
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-bold tracking-tight text-gray-900">
            Pembayaran
        </h1>
        <p class="mt-2 text-sm text-gray-700">
            Pembayaran yang belum selesai. Klik tombol "Lihat Detail" untuk melihat detail pembayaran dan menyelesaikan pembayaran.
        </p>
    </div>
    <div class="space-y-4">
        @foreach($this->getPendingPayments() as $payment)
            <div class="bg-white border border-gray-300 rounded-md p-4 flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-900">Kode Pembayaran: {{ $payment->payment_code }}</p>
                    <p class="text-sm text-gray-500">Total: Rp {{ number_format($payment->amount, 0, ",", ".") }}</p>
                    <p class="text-sm text-gray-500">Payment Method: {{ $payment->paymentMethod->name }}</p>
                </div>
                <a href="{{ route("payment.view", $payment->payment_code) }}" target="_blank" class="py-2 px-4 bg-blue-600 text-white rounded-md text-sm">Lihat Detail</a>
            </div>
        @endforeach
        @if($this->getPendingPayments()->isEmpty())
            <p class="text-sm text-gray-500">Tidak ada pembayaran yang pending.</p>
        @endif
        {{ $this->getPendingPayments()->links() }}
    </div>
</div>