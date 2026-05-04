<?php

use CodeWithDiki\ProductModule\Models\Category;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Livewire\Component;

new class extends Component
{
    public Model $selected_category;
    public Collection $categories;

    public function mount(Collection $categories)
    {
        $this->selected_category = $categories->first();
        $this->categories = $categories;
    }

    public function getProducts() : Collection
    {
        return \CodeWithDiki\ProductModule\Models\Product::whereHas('categories', function($query) {
            $query->where('id', $this->selected_category->id);
        })
        ->latest()
        ->take(6)
        ->get();
    }

    public function changeCategory(Category $category)
    {
        $this->selected_category = $category;
    }


};
?>

<div class="space-y-6 md:space-y-9">
    <div class="flex flex-nowrap gap-3 items-center justify-center">
        @foreach ($categories as $category)
            <div 
                @click="changeCategory({{ $category }})" 
                class="px-3 py-1 rounded-full text-sm font-medium {{ $selected_category->id === $category->id ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-700' }}"
            >
                {{ $category->name }}
            </div>
        @endforeach
    </div>
</div>