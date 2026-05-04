@props([
    "banners" => [],
    "autoplay" => false
])
<div class="w-full carousel container mx-auto relative @if($autoplay) autoplay @endif">
    @foreach($banners as $banner)
        @if($loop->first)
            <figure class="block w-full carousel-item h-auto relative transition delay-150 duration-300 ease-in-out">
                <img src="{{ $banner['image_url'] }}" alt="banner" class="w-full h-full object-contain rounded-md">
                <a href="{{ $banner['link_url'] }}" class="absolute inset-0"></a>
            </figure>
        @else
            <figure class="w-full carousel-item h-auto relative transition delay-150 duration-300 ease-in-out">
                <img src="{{ $banner['image_url'] }}" alt="banner" class="w-full h-full object-contain rounded-md">
                <a href="{{ $banner['link_url'] }}" class="absolute inset-0"></a>
            </figure>
        @endif
    @endforeach
</div>
