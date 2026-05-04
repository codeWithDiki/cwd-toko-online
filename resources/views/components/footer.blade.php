<footer class="bg-white py-6 mt-12 px-2">
    <div class="container mx-auto space-y-6">
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between">
            <div class="flex items-center gap-2 shrink-0">
                <h3 class="font-bold text-lg">
                    {{ $siteSettings->site_name }}
                </h3>
            </div>
            <div>
                <a href="#" class="block md:inline text-gray-700 hover:text-gray-900 px-0 md:px-3 py-2 rounded-md text-sm font-medium">
                    Home
                </a>
                @if($siteSettings->facebook_url)
                    <a href="{{ $siteSettings->facebook_url }}" class="block md:inline text-gray-700 hover:text-gray-900 px-0 md:px-3 py-2 rounded-md text-sm font-medium">
                        Facebook
                    </a>
                @endif
                @if($siteSettings->twitter_url)
                    <a href="{{ $siteSettings->twitter_url }}" class="block md:inline text-gray-700 hover:text-gray-900 px-0 md:px-3 py-2 rounded-md text-sm font-medium">
                        Twitter
                    </a>
                @endif
                @if($siteSettings->instagram_url)
                    <a href="{{ $siteSettings->instagram_url }}" class="block md:inline text-gray-700 hover:text-gray-900 px-0 md:px-3 py-2 rounded-md text-sm font-medium">
                        Instagram
                    </a>
                @endif
                @if($siteSettings->tiktok_url)
                    <a href="{{ $siteSettings->tiktok_url }}" class="block md:inline text-gray-700 hover:text-gray-900 px-0 md:px-3 py-2 rounded-md text-sm font-medium">
                        Tiktok
                    </a>
                @endif

            </div>
        </div>
        <div class="mx-auto text-center text-sm text-gray-500">
            &copy; {{ date('Y') }} <a href="https://instagram.com/dikiasyidiq">Diki Akbar Asyidiq</a>. All rights reserved.
        </div>
    </div>
</footer>