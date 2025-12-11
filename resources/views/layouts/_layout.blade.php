<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:400,500,600,700,800,900|Poppins:400,500,600,700,800,900&amp;subset=latin">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
    <!-- Scripts -->
    @filamentStyles
    @vite(['resources/css/app.css'])

    @yield('headItems')

    @livewireStyles

</head>

<body class="antialiased  @yield('bodyClasses')">
    <header>
        @include('components.navigation-bar')
    </header>
    <div class=" @yield('firstDivClasses')">
        @livewire('notifications')
        @yield('body')
    </div>
    @vite(['resources/js/app.js'])


    @include('components.footer')
    @filamentScripts
    @livewireScripts

</body>

</html>