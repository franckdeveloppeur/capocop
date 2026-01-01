<x-guest-layout>
    <section data-section-id="1" data-share="" data-category="sign-in" data-component-id="fed93509_05_awz" class="relative overflow-hidden">
        <div class="relative flex flex-wrap">
            <div class="w-full lg:w-1/2 px-4">
                <div class="flex items-center justify-center w-full h-full">
                    <div class="max-w-sm pt-24 pb-20 lg:pt-20 mx-auto">
                        <x-validation-errors class="mb-4" />
                        @if (session('status'))
                            <div class="mb-4 font-medium text-sm text-green-600">{{ session('status') }}</div>
                        @endif

                        <form method="POST" action="{{ route('login') }}">
                            @csrf

                            <p class="uppercase text-rhino-300 text-xs font-bold tracking-widest mb-1 text-center">{{ __('auth.sign_in') }}</p>
                            <h1 class="font-heading font-semibold text-4xl text-rhino-700 text-center mb-8">{{ __('auth.join_community') }}</h1>

                            <div class="flex flex-col gap-1 mb-4">
                                <label class="text-coolGray-700 text-sm font-medium mb-2" for="email">{{ __('auth.email_address') }}</label>
                                <x-input id="email" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" :placeholder="__('auth.placeholder_email')" />
                                @error('email')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                            </div>

                            <div class="flex flex-col gap-1 mb-4">
                                <label class="text-coolGray-700 text-sm font-medium mb-2" for="password">{{ __('auth.password') }}</label>
                                <x-password-input id="password" name="password" required autocomplete="current-password" :placeholder="__('auth.placeholder_password')" />
                                @error('password')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                            </div>

                            <div class="flex items-center justify-between mb-6">
                                <label for="remember_me" class="flex items-center gap-2">
                                    <x-checkbox id="remember_me" name="remember" />
                                    <span class="text-sm text-coolGray-700">{{ __('auth.remember_me') }}</span>
                                </label>
                                @if (Route::has('password.request'))
                                    <a class="text-sm text-indigo-600 hover:underline font-medium" href="{{ route('password.request') }}">{{ __('auth.forgot_password') }}</a>
                                @endif
                            </div>

                            <x-button class="rounded-sm py-3 px-4 bg-purple-500 shadow-md text-white font-medium text-sm w-full hover:bg-purple-600 focus:outline-none focus:ring-2 focus:ring-purple-400 focus:ring-offset-2 transition duration-200 mb-4">{{ __('auth.log_in') }}</x-button>

                            <a class="mb-4 w-full rounded-sm border border-coolGray-200 py-3 px-6 flex items-center justify-center gap-4 text-coolGray-700 hover:bg-purple-500 hover:text-white transition duration-200" href="#">
                                <svg xmlns="http://www.w3.org/2000/svg" width="25" height="24" viewBox="0 0 25 24" fill="none">
                                    <circle cx="12.5" cy="12" r="12" fill="#DDDCFE"></circle>
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M13.044 18V12.5266H15.107L15.4165 10.3929H13.044V9.03088C13.044 8.41332 13.2358 7.99246 14.2318 7.99246L15.5 7.99199V6.08354C15.2807 6.05817 14.5278 6 13.6516 6C11.8219 6 10.5693 6.99418 10.5693 8.81956V10.3929H8.5V12.5266H10.5693V18H13.044Z" fill="#416BE6"></path>
                                </svg>
                                <span class="text-sm font-medium">{{ __('auth.sign_in_facebook') }}</span>
                            </a>

                            <a class="w-full rounded-sm border border-coolGray-200 py-3 px-6 flex items-center justify-center gap-4 text-coolGray-700 hover:bg-purple-500 hover:text-white transition duration-200" href="#">
                                <img src="/coleos-assets/logos/google-logo.svg" alt="Google logo">
                                <span class="text-sm font-medium">{{ __('auth.sign_in_google') }}</span>
                            </a>

                            <p class="text-center text-sm text-coolGray-700 mt-4">
                                {{ __('auth.no_account') }}
                                <a href="{{ route('register') }}" class="text-indigo-600 hover:underline font-medium">{{ __('auth.sign_up') }}</a>
                            </p>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Section image avec textes qui swipent -->
            <div class="relative lg:absolute top-0 right-0 w-full lg:w-1/2 block h-112 lg:h-full overflow-hidden bg-gradient-to-br from-purple-600 via-purple-700 to-purple-800" x-data="{
                currentSlide: 0,
                autoplayInterval: null,
                slides: [
                    {
                        title: 'Bienvenue chez Capocop',
                        description: 'Votre partenaire de confiance pour tous vos besoins quotidiens. Qualité, service et facilités de paiement à votre portée.'
                    },
                    {
                        title: 'Paiement Échelonné',
                        description: 'Achetez maintenant et payez progressivement selon vos moyens. Une solution adaptée à votre budget.'
                    },
                    {
                        title: 'Service Client Exceptionnel',
                        description: 'Notre équipe est à votre écoute pour vous accompagner dans tous vos projets et répondre à vos besoins.'
                    },
                    {
                        title: 'Produits de Qualité',
                        description: 'Découvrez une large gamme de produits sélectionnés avec soin pour répondre à vos attentes.'
                    }
                ],
                startAutoplay() {
                    this.autoplayInterval = setInterval(() => {
                        this.nextSlide();
                    }, 5000);
                },
                stopAutoplay() {
                    clearInterval(this.autoplayInterval);
                },
                nextSlide() {
                    this.currentSlide = (this.currentSlide + 1) % this.slides.length;
                },
                prevSlide() {
                    this.currentSlide = this.currentSlide === 0 ? this.slides.length - 1 : this.currentSlide - 1;
                }
            }" x-init="startAutoplay()" @mouseenter="stopAutoplay()" @mouseleave="startAutoplay()">
                <!-- Image fixe en fond -->
                <div class="absolute inset-0">
                    <img class="w-full h-full object-cover opacity-20" src="/coleos-assets/nav/1.png" alt="Capocop">
                </div>
                
                <!-- Textes qui swipent -->
                <div class="absolute inset-0 flex flex-col items-center justify-center px-8 text-center z-10">
                    <template x-for="(slide, index) in slides" :key="index">
                        <div x-show="currentSlide === index" 
                             x-transition:enter="transition ease-out duration-700" 
                             x-transition:enter-start="opacity-0 transform translate-y-4" 
                             x-transition:enter-end="opacity-100 transform translate-y-0" 
                             x-transition:leave="transition ease-in duration-700" 
                             x-transition:leave-start="opacity-100 transform translate-y-0" 
                             x-transition:leave-end="opacity-0 transform -translate-y-4" 
                             class="absolute inset-0 flex flex-col items-center justify-center px-8">
                            <h2 class="text-white text-2xl lg:text-4xl xl:text-5xl font-bold mb-4 drop-shadow-lg" x-text="slide.title"></h2>
                            <p class="text-white text-sm lg:text-lg xl:text-xl opacity-90 max-w-md drop-shadow-md" x-text="slide.description"></p>
                        </div>
                    </template>
                </div>
                
                <!-- Navigation dots -->
                <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 z-20 flex gap-2">
                    <template x-for="(slide, index) in slides" :key="index">
                        <button @click="currentSlide = index" 
                                class="w-2 h-2 rounded-full transition-all duration-300"
                                :class="currentSlide === index ? 'bg-white w-8' : 'bg-white/50'"
                                :aria-label="'Aller à la slide ' + (index + 1)"></button>
                    </template>
                </div>
            </div>
        </div>
    </section>
</x-guest-layout>
