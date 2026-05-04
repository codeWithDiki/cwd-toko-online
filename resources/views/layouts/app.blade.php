<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield("title", $siteSettings->site_name)</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @filamentStyles
</head>

<body>
    <livewire:navbar-component />
    <main class="">
        {{ $slot ?? '' }}
    </main>
    <x-footer />
    <div class="fixed inset-0 z-100 bg-gray-900/50 hidden px-2 flex flex-col justify-center items-center" id="cart-error">
        <div class="w-full max-w-md bg-white rounded-md">
            <div class="p-4">
                <h3 class="text-lg font-semibold mb-2">Error</h3>
                <p class="text-gray-700 mb-4" id="cart-error-message"></p>
                <button class="px-3 py-1 bg-blue-600 text-white rounded-md text-sm" id="cart-error-close">
                    Close
                </button>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener("livewire:navigated", function(){
            Livewire.on("cartError", function(data) {
                const message = data[0].message;
                toastr.error(message, "Error");
            });

            Livewire.on("cartUpdated", function(data) {
                const message = data[0].message ?? null;

                if(message) {
                    toastr.success(message, "Success");
                }
            });

        });
    </script>
    <div class="z-[1000]">
        @livewire('notifications')
    </div>
    @filamentScripts
</body>

</html>