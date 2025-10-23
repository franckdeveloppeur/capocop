<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:400,500,600,700,800,900|Poppins:400,500,600,700,800,900&amp;subset=latin">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="{{ Vite::asset('resources/css/tailwind/tailwind.min.css') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ Vite::asset('resources/shuffle-for-tailwind.png') }}">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js" defer></script>
    @yield('headItems')
</head>
<body class="antialiased bg-body text-body font-body @yield('bodyClasses')">
    <div class=" @yield('firstDivClasses')">
            @yield('body')
    <script type="text/javascript" src="{{ Vite::asset('resources/js/global-10087.js') }}?v=1761179280"></script></div>
    @yield('footerItems')
</body>
</html>

