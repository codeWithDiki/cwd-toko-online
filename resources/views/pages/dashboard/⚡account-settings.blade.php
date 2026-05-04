<?php

use App\Models\User;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout("layouts.dashboard")] class extends Component
{
    public User $user;

    public ?string $name = null;
    public ?string $email = null;
    public ?string $password = null;
    public ?string $password_confirmation = null;

    public function mount()
    {
        $this->user = Auth::user();
        $this->name = $this->user->name;
        $this->email = $this->user->email;
    }

    public function save()
    {
        $this->validate([
            "name" => ["required", "string", "max:255"],
            "email" => ["required", "email", "max:255", "unique:users,email,{$this->user->id}"],
            "password" => ["nullable", "string", "min:8", "confirmed"],
        ]);

        $this->user->name = $this->name;
        $this->user->email = $this->email;

        if($this->password) {
            $this->user->password = bcrypt($this->password);
        }

        $this->user->save();

        Notification::make()
            ->title("Pengaturan akun berhasil disimpan")
            ->success()
            ->send();
    }

};
?>

<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-bold tracking-tight text-gray-900">
            Pengaturan Akun
        </h1>
    </div>
    <div class="bg-white rounded-md border border-gray-200 space-y-3 p-3">
        <form wire:submit.prevent="save" class="space-y-3">
            <div class="space-y-2">
                <label for="name" class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                <input type="text" id="name" wire:model.defer="name" class="py-2 block w-full px-3 rounded-md border border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                @error("name") <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
            <div class="space-y-2">
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" id="email" wire:model.defer="email" class="py-2 block w-full px-3 rounded-md border border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                @error("email") <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
            <hr class="border-gray-200">
            <p class="text-sm text-gray-400">
                Biarkan kolom password kosong jika kamu tidak ingin mengganti password.
            </p>
            <div class="space-y-2">
                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                <input type="password" id="password" wire:model.defer="password" class="py-2 block w-full px-3 rounded-md border border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                @error("password") <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
            <div class="space-y-2">
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Ketik ulang password</label>
                <input type="password" id="password_confirmation" wire:model.defer="password_confirmation" class="py-2 block w-full px-3 rounded-md border border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                @error("password_confirmation") <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
            <div class="flex justify-start mt-6">
                <button wire:loading.attr="disabled" type="submit" class="px-4 py-2 bg-black text-white text-sm rounded-md hover:bg-black/80 transition duration-200">
                    <span class="block">
                        Simpan Perubahan
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>