<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield("title", $siteSettings->site_name)</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#B6B6B6]/20">
    <main class="container mx-auto px-2">
        {{ $slot ?? '' }}
    </main>
</body>
</html>