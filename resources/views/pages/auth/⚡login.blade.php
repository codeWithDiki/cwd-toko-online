<?php

use DanHarrin\LivewireRateLimiting\WithRateLimiting;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts.simple')] class extends Component
{
    use WithRateLimiting;

    public ?string $email = null;
    public ?string $password = null;
    public bool $remember = false;

    public function login()
    {
        try {
            $this->rateLimit(3, 60);

            $this->validate([
                "email" => ["required", "email"],
                "password" => ["required"],
            ]);

            if (Auth::attempt([
                "email" => $this->email,
                "password" => $this->password,
            ], $this->remember)) {
                session()->regenerate();
                return redirect()->intended(route("product.explore"));
            }

            $this->addError("email", "The provided credentials do not match our records.");
        } catch(\DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException $e) {
            $this->addError("limit", "Too many login attempts. Please try again in " . $e->secondsUntilAvailable . " seconds.");
            return;
        }
        
    }

};
?>
@section("title", "Login - {$siteSettings->site_name}")
<div class="flex flex-col items-center justify-center min-h-screen">
    <div class="w-full max-w-md space-y-3">
        <h1 class="text-2xl text-center">
            Login to Your Account
        </h1>
        <form wire:submit.prevent="login" class="bg-white w-full max-w-md rounded-lg shadow-lg px-4 py-9 space-y-3">
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
            <div class="flex items-center gap-2">
                <input type="checkbox" id="remember" wire:model.defer="remember" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                <label for="remember" class="block text-sm text-gray-900">Remember Me</label>
            </div>
            @error("limit") <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            <button type="submit" class="w-full bg-indigo-600 text-white py-2 rounded-md hover:bg-indigo-700 transition duration-200">
                Login
            </button>
            <a href="{{ route('register') }}" class="text-xs text-blue-600">Belum punya akun ? Register disini</a>
        </form>
    </div>
</div>