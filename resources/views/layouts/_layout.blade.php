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
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
    @vite(['resources/css/app.css'])

    @yield('headItems')

    @livewireStyles

</head>

<body class="antialiased  @yield('bodyClasses')">
    <header>
        @include('components.navigation-bar')
    </header>
    <div class=" @yield('firstDivClasses')">
       
        <x-toast-notification />
        @yield('body')
    </div>
   
    <!-- WhatsApp Floating Button -->
    <a href="https://wa.me/237695664661?text=Bonjour%2C%20j'ai%20une%20question" 
       target="_blank" 
       rel="noopener noreferrer"
       class="whatsapp-float"
       aria-label="Contactez-nous sur WhatsApp">
      <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 24 24">
        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
      </svg>
    </a>

    <style>
      .whatsapp-float {
        position: fixed;
        width: 60px;
        height: 60px;
        bottom: 85px;
        right: 20px;
        background: linear-gradient(135deg, #25D366 0%, #128C7E 100%);
        color: #FFF;
        border-radius: 50%;
        text-align: center;
        font-size: 30px;
        box-shadow: 0 4px 12px rgba(37, 211, 102, 0.4);
        z-index: 100;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        animation: whatsapp-pulse 2s ease-in-out infinite;
      }

      .whatsapp-float:hover {
        transform: scale(1.1) rotate(5deg);
        box-shadow: 0 6px 20px rgba(37, 211, 102, 0.6);
        background: linear-gradient(135deg, #128C7E 0%, #25D366 100%);
      }

      .whatsapp-float:active {
        transform: scale(0.95);
      }

      .whatsapp-float::before {
        content: '';
        position: absolute;
        width: 100%;
        height: 100%;
        border-radius: 50%;
        background: rgba(37, 211, 102, 0.3);
        animation: whatsapp-ripple 2s ease-out infinite;
        z-index: -1;
      }

      @keyframes whatsapp-pulse {
        0%, 100% {
          transform: scale(1);
        }
        50% {
          transform: scale(1.05);
        }
      }

      @keyframes whatsapp-ripple {
        0% {
          transform: scale(1);
          opacity: 1;
        }
        100% {
          transform: scale(1.8);
          opacity: 0;
        }
      }

      /* Responsive adjustments */
      @media (max-width: 767px) {
        .whatsapp-float {
          width: 56px;
          height: 56px;
          bottom: 80px;
          right: 16px;
        }
        .whatsapp-float svg {
          width: 28px;
          height: 28px;
        }
      }

      @media (min-width: 768px) {
        .whatsapp-float {
          bottom: 30px;
          right: 30px;
        }
      }
    </style>

    @include('components.footer')
    @vite(['resources/js/app.js'])
    @filamentScripts
    @livewireScripts
    
    @stack('scripts')

</body>

</html>

   