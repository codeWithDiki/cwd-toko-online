<?php

use CodeWithDiki\ProductModule\Facades\ProductModule;
use Illuminate\Http\Request;
use Livewire\Attributes\Layout;
use Livewire\Component;

new class extends Component
{
    public ?string $search = null;
    public ?int $category_id = null;
    public ?int $brand_id = null;
    public ?string $tag = null;

    public function mount(Request $request)
    {
        if($request->has('q')) {
            $this->search = $request->query('q');
        }

        if($request->has('category')) {
            $category = \CodeWithDiki\ProductModule\Models\Category::where('slug', $request->query('category'))->first();
            if($category) {
                $this->category_id = $category->id;
            }
        }

        if($request->has('brand')) {
            $brand = \CodeWithDiki\ProductModule\Models\Brand::where('slug', $request->query('brand'))->first();
            if($brand) {
                $this->brand_id = $brand->id;
            }
        }

        if($request->has('tag')) {
            $this->tag = $request->query('tag');
        }

    }

    public function getCategories() : \Illuminate\Support\Collection
    {
        return \CodeWithDiki\ProductModule\Models\Category::where('is_active', true)->get();
    }

    public function getBrands() : \Illuminate\Support\Collection
    {
        return \CodeWithDiki\ProductModule\Models\Brand::where('is_active', true)->get();
    }

    public function getProducts() : \Illuminate\Support\Collection
    {
        return \CodeWithDiki\ProductModule\Models\Product::query()
            ->when($this->search, function($query) {
                $query->where('name', 'like', "%{$this->search}%");
            })
            ->when($this->category_id, function($query) {
                $query->whereHas('categories', function($query) {
                    $query->where('category_id', $this->category_id);
                });
            })
            ->when($this->brand_id, function($query) {
                $query->where('brand_id', $this->brand_id);
            })
            ->when($this->tag, function($query) {
                $query->whereJsonContains('tags', $this->tag);
            })
            ->latest()
            ->get();
    }

    public function getTags() : array
    {
        return ProductModule::getTags();
    }

};
?>
@section("title", "Explore Products - {$siteSettings->site_name}")
<div class="container mx-auto px-2 py-3 min-h-screen">
    <div class="flex items-start gap-6">
        <div class="w-64 shrink-0 hidden md:block border-r border-gray-300 pr-4 min-h-screen">
            <h3 class="font-semibold mb-3">
                Kategori
            </h3>
            <div class="mb-6 flex flex-wrap gap-2 items-center">
                @foreach ($this->getCategories() as $category)
                    <div 

                        class="px-3 py-1 rounded-lg text-sm font-medium cursor-pointer relative {{ $category_id === $category->id ? 'bg-black text-white' : 'bg-gray-200 text-gray-700' }}"
                    >
                        {{ $category->name }}
                        <a href="{{ route('product.explore', [
                            'category' => $category->slug,
                            'brand' => $this->brand_id ? \CodeWithDiki\ProductModule\Models\Brand::find($this->brand_id)->slug : null,
                            'tag' => $this->tag,
                            'q' => $this->search,
                        ]) }}" wire:navigate class="absolute inset-0"></a>
                    </div>
                @endforeach
            </div>

            <h3 class="font-semibold mb-3">
                Brands
            </h3>
            <div class="mb-6 flex flex-wrap gap-2 items-center">
                @foreach ($this->getBrands() as $brand)
                    <div
                        class="px-3 py-1 rounded-lg text-sm font-medium cursor-pointer relative {{ $brand_id === $brand->id ? 'bg-black text-white' : 'bg-gray-200 text-gray-700' }}"
                    >
                        {{ $brand->name }}
                        <a href="{{ route('product.explore', [
                            'category' => $this->category_id ? \CodeWithDiki\ProductModule\Models\Category::find($this->category_id)->slug : null,
                            'brand' => $brand->slug,
                            'tag' => $this->tag,
                            'q' => $this->search,
                        ]) }}" wire:navigate class="absolute inset-0"></a>
                    </div>
                @endforeach
            </div>

            <h3 class="font-semibold mb-3">
                Tags
            </h3>
            <div class="mb-6 flex flex-wrap gap-2 items-center">
                @foreach ($this->getTags() as $tag)
                    <div
                        class="px-3 py-1 rounded-lg text-sm font-medium cursor-pointer relative {{ $this->tag === $tag ? 'bg-black text-white' : 'bg-gray-200 text-gray-700' }}"
                    >
                        {{ $tag }}
                        <a href="{{ route('product.explore', [
                            'category' => $this->category_id ? \CodeWithDiki\ProductModule\Models\Category::find($this->category_id)->slug : null,
                            'brand' => $this->brand_id ? \CodeWithDiki\ProductModule\Models\Brand::find($this->brand_id)->slug : null,
                            'tag' => $tag,
                            'q' => $this->search,
                        ]) }}" wire:navigate class="absolute inset-0"></a>
                    </div>
                @endforeach
            </div>

            <a class="px-2 py-1 bg-gray-200 text-gray-700 rounded-md w-full block text-center" href="{{ route('product.explore') }}" wire:navigate>
                Clear Filters
            </a>

        </div>
        <div class="flex-1 space-y-3 relative" x-data="{
            showFilter: false
        }">
            <div class="w-full pb-3 border-b border-gray-200 space-y-2">
                <div class="flex items-center justify-between gap-3">
                    <input 
                        type="text"
                        wire:model.live.debounce.200ms="search" 
                        placeholder="Search products..." 
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                    <div class="md:hidden shrink-0 py-2 rounded-md border px-2 border-gray-300 cursor-pointer" @click="showFilter = !showFilter">
                        @svg("heroicon-o-funnel", "w-6 h-6 text-gray-500")
                    </div>
                </div>
                @if($this->search)
                    <p class="text-sm text-gray-500 mt-1">
                        Menampilkan untuk pencarian "{{ $this->search }}"
                    </p>
                @endif
                <div>
                    <div class="flex items-center flex-nowrap gap-1 w-full overflow-x-auto pb-2">
                        @if ($category_id)
                            <div class="px-3 py-1 rounded-lg text-sm font-medium bg-gray-300 text-gray-600 relative shrink-0">
                                Kategori : {{ \CodeWithDiki\ProductModule\Models\Category::find($category_id)->name }}
                            </div>
                        @endif

                        @if ($brand_id)
                            <div class="px-3 py-1 rounded-lg text-sm font-medium bg-gray-300 text-gray-600 relative shrink-0">
                                Brand : {{ \CodeWithDiki\ProductModule\Models\Brand::find($brand_id)->name }}
                            </div>
                        @endif

                        @if ($this->tag)
                            <div class="px-3 py-1 rounded-lg text-sm font-medium bg-gray-300 text-gray-600 relative shrink-0">
                                Tag : {{ $this->tag }}
                            </div>
                        @endif
                        @if ($this->search)
                            <div class="px-3 py-1 rounded-lg text-sm font-medium bg-gray-300 text-gray-600 relative shrink-0">
                                Pencarian : {{ $this->search }}
                            </div>
                        @endif

                    </div>
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse ($this->getProducts() as $product)
                    <x-product-card :product="$product" display="maximum" />
                @empty
                    <div class="col-span-full text-center py-12">
                        <h3 class="text-2xl font-semibold mb-2">
                            Tidak ada produk yang ditemukan
                        </h3>
                        <p class="text-gray-500">
                            Coba periksa kembali kata kunci pencarian atau kategori yang Anda pilih.
                        </p>
                    </div>
                @endforelse
            </div>
            <div class="absolute top-12 z-10 w-full rounded-md p-3 bg-white border border-gray-300 shadow-lg" 
            style="display: none;"
            x-show="showFilter" 
            x-transition
            @click.away="showFilter = false">
                <h3 class="font-semibold mb-3">
                    Categories
                </h3>
                <div class="mb-6 flex flex-wrap gap-2 items-center">
                    @foreach ($this->getCategories() as $category)
                        <div 

                            class="px-3 py-1 rounded-lg text-sm font-medium cursor-pointer relative {{ $category_id === $category->id ? 'bg-black text-white' : 'bg-gray-200 text-gray-700' }}"
                        >
                            {{ $category->name }}
                            <a href="{{ route('product.explore', [
                                'category' => $category->slug,
                                'brand' => $this->brand_id ? \CodeWithDiki\ProductModule\Models\Brand::find($this->brand_id)->slug : null,
                                'tag' => $this->tag,
                                'q' => $this->search,
                            ]) }}" wire:navigate class="absolute inset-0"></a>
                        </div>
                    @endforeach
                </div>

                <h3 class="font-semibold mb-3">
                    Brands
                </h3>
                <div class="mb-6 flex flex-wrap gap-2 items-center">
                    @foreach ($this->getBrands() as $brand)
                        <div
                            class="px-3 py-1 rounded-lg text-sm font-medium cursor-pointer relative {{ $brand_id === $brand->id ? 'bg-black text-white' : 'bg-gray-200 text-gray-700' }}"
                        >
                            {{ $brand->name }}
                            <a href="{{ route('product.explore', [
                                'category' => $this->category_id ? \CodeWithDiki\ProductModule\Models\Category::find($this->category_id)->slug : null,
                                'brand' => $brand->slug,
                                'tag' => $this->tag,
                                'q' => $this->search,
                            ]) }}" wire:navigate class="absolute inset-0"></a>
                        </div>
                    @endforeach
                </div>

                <h3 class="font-semibold mb-3">
                    Tags
                </h3>
                <div class="mb-6 flex flex-wrap gap-2 items-center">
                    @foreach ($this->getTags() as $tag)
                        <div
                            class="px-3 py-1 rounded-lg text-sm font-medium cursor-pointer relative {{ $this->tag === $tag ? 'bg-black text-white' : 'bg-gray-200 text-gray-700' }}"
                        >
                            {{ $tag }}
                            <a href="{{ route('product.explore', [
                                'category' => $this->category_id ? \CodeWithDiki\ProductModule\Models\Category::find($this->category_id)->slug : null,
                                'brand' => $this->brand_id ? \CodeWithDiki\ProductModule\Models\Brand::find($this->brand_id)->slug : null,
                                'tag' => $tag,
                                'q' => $this->search,
                            ]) }}" wire:navigate class="absolute inset-0"></a>
                        </div>
                    @endforeach
                </div>

                <a class="px-2 py-1 bg-gray-200 text-gray-700 rounded-md w-full block text-center" href="{{ route('product.explore') }}" wire:navigate>
                    Clear Filters
                </a>
            </div>
        </div>
    </div>
</div>