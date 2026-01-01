<x-guest-layout>
    <section data-section-id="1" data-share="" data-category="sign-up" data-component-id="fed93509_05_awz" class="relative overflow-hidden">
        <div class="relative flex flex-wrap">
            <div class="w-full lg:w-1/2 px-4">
                <div class="flex items-center justify-center w-full h-full">
                    <div class="max-w-md lg:max-w-lg pt-24 pb-20 lg:pt-20 mx-auto w-full">
                        <x-validation-errors class="mb-4" />

                        <form method="POST" action="{{ route('register') }}">
                            @csrf

                            <p class="uppercase text-rhino-300 text-xs font-bold tracking-widest mb-1 text-center">{{ __('auth.sign_up') }}</p>
                            <h1 class="font-heading font-semibold text-4xl text-rhino-700 text-center mb-8">{{ __('auth.create_account') }}</h1>

                            <div class="flex flex-col gap-1 mb-4">
                                <label class="text-coolGray-700 text-sm font-medium mb-2" for="name">{{ __('auth.name') }}</label>
                                <x-input id="name" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" :placeholder="__('auth.placeholder_name')" />
                                @error('name')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                            </div>

                            <div class="flex flex-col gap-1 mb-4">
                                <label class="text-coolGray-700 text-sm font-medium mb-2" for="email">{{ __('auth.email') }}</label>
                                <x-input id="email" type="email" name="email" :value="old('email')" required autocomplete="username" :placeholder="__('auth.placeholder_email')" />
                                @error('email')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                            </div>

                            <div class="flex flex-col gap-1 mb-4">
                                <label class="text-coolGray-700 text-sm font-medium mb-2" for="phone">{{ __('auth.phone') }}</label>
                                <x-input id="phone" type="tel" name="phone" :value="old('phone')" autocomplete="tel" :placeholder="__('auth.placeholder_phone')" />
                                @error('phone')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                            </div>

                            <div class="flex flex-col gap-1 mb-4">
                                <label class="text-coolGray-700 text-sm font-medium mb-2" for="password">{{ __('auth.password') }}</label>
                                <x-password-input id="password" name="password" required autocomplete="new-password" :placeholder="__('auth.placeholder_password')" />
                                @error('password')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                            </div>

                            <div class="flex flex-col gap-1 mb-6">
                                <label class="text-coolGray-700 text-sm font-medium mb-2" for="password_confirmation">{{ __('auth.confirm_password') }}</label>
                                <x-password-input id="password_confirmation" name="password_confirmation" required autocomplete="new-password" :placeholder="__('auth.placeholder_confirm_password')" />
                                @error('password_confirmation')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                            </div>

                            @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
                                <div class="mb-6 flex items-start gap-3">
                                    <x-checkbox name="terms" id="terms" required />
                                    <label for="terms" class="text-sm text-coolGray-700">
                                        {{ __('auth.i_agree_to') }}
                                        <a target="_blank" href="{{ route('terms.show') }}" class="text-indigo-600 hover:underline font-medium">{{ __('auth.terms_of_service') }}</a>
                                        {{ __('auth.and') }}
                                        <a target="_blank" href="{{ route('policy.show') }}" class="text-indigo-600 hover:underline font-medium">{{ __('auth.privacy_policy') }}</a>
                                    </label>
                                </div>
                            @endif

                            <x-button class="rounded-sm py-3 px-4 bg-purple-500 shadow-md text-white font-medium text-sm w-full hover:bg-purple-600 focus:outline-none focus:ring-2 focus:ring-purple-400 focus:ring-offset-2 transition duration-200 mb-4">{{ __('auth.register') }}</x-button>

                            <p class="text-center text-sm text-coolGray-700 mt-4">
                                {{ __('auth.already_registered') }}
                                <a href="{{ route('login') }}" class="text-indigo-600 hover:underline font-medium">{{ __('auth.sign_in') }}</a>
                            </p>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Section image avec textes qui swipent -->
            <div class="relative lg:absolute top-0 right-0 w-full lg:w-1/2 block h-112 lg:h-full overflow-hidden bg-gradient-to-br from-indigo-600 via-indigo-700 to-purple-800" x-data="{
                currentSlide: 0,
                autoplayInterval: null,
                slides: [
                    {
                        title: 'Rejoignez la Communauté',
                        description: 'Créez votre compte et accédez à tous nos services. Inscription simple et rapide en quelques clics.'
                    },
                    {
                        title: 'Avantages Exclusifs',
                        description: 'Profitez d\'offres spéciales, de réductions et de services personnalisés réservés à nos membres.'
                    },
                    {
                        title: 'Suivi en Temps Réel',
                        description: 'Suivez vos commandes et paiements en direct depuis votre espace personnel sécurisé.'
                    },
                    {
                        title: 'Support Dédié',
                        description: 'Notre équipe est disponible pour vous accompagner à chaque étape de votre parcours.'
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
                    <img class="w-full h-full object-cover opacity-20" src="/coleos-assets/nav/6.png" alt="Capocop">
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
