<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts.dashboard')] class extends Component
{
    public User $user;

    public function mount()
    {
        $this->user = Auth::user();
    }
};
?>
@section("title", "Dashboard - {$siteSettings->site_name}")
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-bold tracking-tight text-gray-900">
            Dashboard
        </h1>
        <p class="mt-2 text-sm text-gray-700">
            Lihat riwayat transaksi kamu di bawah ini.
        </p>
    </div>
    <livewire:dashboard.customer-transaction-table-component :user="$user" />
</div>