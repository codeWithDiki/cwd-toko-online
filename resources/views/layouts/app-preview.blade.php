<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield("title", $siteSettings->site_name)</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @masonStyles
</head>
<body>
    <livewire:navbar-component />
    <main class="text-black">
        @include('mason::iframe-preview-content', ['blocks' => $blocks])
    </main>
    <x-footer />
</body>
</html>