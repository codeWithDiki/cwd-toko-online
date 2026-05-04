<?php

use App\Enums\UserRole;
use DanHarrin\LivewireRateLimiting\WithRateLimiting;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout("layouts.simple")] class extends Component
{
    use WithRateLimiting;

    public ?string $name = null;
    public ?string $email = null;
    public ?string $password = null;
    public ?string $password_confirmation = null;

    public function register()
    {
        try {
            $this->rateLimit(3, 60);
            $this->validate([
                "name" => ["required", "string", "max:255"],
                "email" => ["required", "email", "max:255", "unique:users,email"],
                "password" => ["required", "string", "min:8", "confirmed"],
            ]);

            $user = \App\Models\User::create([
                "name" => $this->name,
                "email" => $this->email,
                "password" => bcrypt($this->password),
            ]);

            Auth::login($user);

            $user->assignRole(UserRole::Customer);

            return redirect()->intended(route("product.explore"));
        } catch(\DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException $e) {
            $this->addError("limit", "Too many login attempts. Please try again in " . $e->secondsUntilAvailable . " seconds.");
            return;
        }
    }

};
?>
@section("title", "Register - {$siteSettings->site_name}")
<div class="flex flex-col items-center justify-center min-h-screen">
    <div class="w-full max-w-md space-y-3">
        <h1 class="text-2xl text-center">
            Create New Account
        </h1>
        <form wire:submit.prevent="register" class="bg-white w-full max-w-md rounded-lg shadow-lg px-4 py-9 space-y-3">
            <div class="space-y-2">
                <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                <input type="name" id="name" wire:model.defer="name" class="py-2 px-3 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                @error("name") <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
            <div class="space-y-2">
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" id="email" wire:model.defer="email" class="py-2 px-3 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                @error("email") <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
            <div class="space-y-2">
                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                <input type="password" id="password" wire:model.defer="password" class="py-2 px-3 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                @error("password") <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
            <div class="space-y-2">
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Password Confirmation</label>
                <input type="password" id="password_confirmation" wire:model.defer="password_confirmation" class="py-2 px-3 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                @error("password_confirmation") <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
            @error("limit") <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            <button type="submit" class="w-full bg-indigo-600 text-white py-2 rounded-md hover:bg-indigo-700 transition duration-200">
                Login
            </button>
            <a href="{{ route('login') }}" class="text-xs text-blue-600">Sudah punya akun ? Login disini</a>
        </form>
    </div>
</div>