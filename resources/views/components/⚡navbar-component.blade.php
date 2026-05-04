<?php

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;

new class extends Component
{
    public ?string $search = null;
    public ?string $site_name = null;
    public ?string $site_logo = null;

    public function mount()
    {
        $settings = app(\App\Settings\SiteSettings::class);
        $this->site_name = $settings->site_name;
        $this->site_logo = Storage::url($settings->site_logo_url);
    }

    public function getCategories() : Collection
    {
        return \CodeWithDiki\ProductModule\Models\Category::where('is_active', true)->get();
    }

};
?>

<div class="sticky top-0 z-10">
    <nav class="bg-white px-2 py-2.5 shadow-md">
        <div class="container flex flex-wrap justify-between gap-3 items-center mx-auto">
            <div class="flex items-center gap-2 shrink-0 relative">
                <h3 class="font-bold text-lg">
                    {{ $site_name }}
                </h3>
                <a href="{{ route('home') }}" wire:navigate class="absolute inset-0"></a>
            </div>
            <div class="hidden flex-1 lg:flex gap-2 items-center justify-center">
                @foreach ($this->getCategories() as $category)
                    <a href="{{ route("product.explore", ["category" => $category->slug]) }}" wire:navigate class="text-gray-700 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium">
                        {{ $category->name }}
                    </a>
                @endforeach
            </div>
            <div class="hidden md:flex items-center gap-3">
                @guest
                    <a href="{{ route("login") }}" wire:navigate class="text-gray-700 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium">
                        Login
                    </a>
                @endif
                @auth
                    <a href="{{ route("dashboard") }}" wire:navigate class="text-gray-700 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium">
                        Dashboard
                    </a>
                @endauth
                <a href="{{ route("product.explore") }}" wire:navigate class="text-gray-700 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium">
                    Explore Products
                </a>
                <div class="p-1 rounded-lg border border-gray-500 cursor-pointer">
                    <livewire:cart-component />
                </div>
            </div>
        </div>
    </nav>
</div>