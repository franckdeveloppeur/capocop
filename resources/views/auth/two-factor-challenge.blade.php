<x-guest-layout>
    <section data-section-id="1" data-share="" data-category="two-factor" data-component-id="fed93509_05_awz" class="relative overflow-hidden">
        <div class="relative flex flex-wrap">
            <div class="w-full lg:w-1/2 px-4">
                <div class="flex items-center justify-center w-full h-full">
                    <div class="max-w-sm pt-24 pb-20 lg:pt-20 mx-auto">
                        <div x-data="{ recovery: false }">
                            <h2 class="text-rhino-700 text-2xl font-semibold mb-4 font-heading text-center">{{ __('auth.two_factor_authentication') }}</h2>

                            <div class="text-center text-sm text-coolGray-600 mb-6" x-show="!recovery">
                                {{ __('auth.two_factor_description') }}
                            </div>

                            <div class="text-center text-sm text-coolGray-600 mb-6" x-cloak x-show="recovery">
                                {{ __('auth.two_factor_recovery_description') }}
                            </div>

                            <x-validation-errors class="mb-4" />

                            <form method="POST" action="{{ route('two-factor.login') }}">
                                @csrf

                                <div class="flex flex-col gap-1 mb-6" x-show="!recovery">
                                    <label class="text-coolGray-700 text-sm font-medium mb-2" for="code">{{ __('auth.code') }}</label>
                                    <x-input id="code" type="text" inputmode="numeric" name="code" autofocus x-ref="code" autocomplete="one-time-code" :placeholder="__('auth.placeholder_code')" />
                                    @error('code')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                                </div>

                                <div class="flex flex-col gap-1 mb-6" x-cloak x-show="recovery">
                                    <label class="text-coolGray-700 text-sm font-medium mb-2" for="recovery_code">{{ __('auth.recovery_code') }}</label>
                                    <x-input id="recovery_code" type="text" name="recovery_code" x-ref="recovery_code" autocomplete="one-time-code" :placeholder="__('auth.placeholder_recovery_code')" />
                                    @error('recovery_code')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                                </div>

                                <div class="flex items-center justify-between gap-4 mb-4">
                                    <button type="button" class="text-sm text-indigo-600 hover:underline cursor-pointer font-medium" x-show="!recovery" @click="recovery = true; $nextTick(() => { $refs.recovery_code.focus() })">
                                        {{ __('auth.use_recovery_code') }}
                                    </button>

                                    <button type="button" class="text-sm text-indigo-600 hover:underline cursor-pointer font-medium" x-cloak x-show="recovery" @click="recovery = false; $nextTick(() => { $refs.code.focus() })">
                                        {{ __('auth.use_authentication_code') }}
                                    </button>
                                </div>

                                <x-button class="rounded-sm py-3 px-4 bg-purple-500 shadow-md text-white font-medium text-sm w-full hover:bg-purple-600 focus:outline-none focus:ring-2 focus:ring-purple-400 focus:ring-offset-2 transition duration-200">{{ __('auth.verify') }}</x-button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section image avec textes qui swipent -->
            <div class="relative lg:absolute top-0 right-0 w-full lg:w-1/2 block h-112 lg:h-full overflow-hidden bg-gradient-to-br from-blue-600 via-purple-600 to-purple-700" x-data="{
                currentSlide: 0,
                autoplayInterval: null,
                slides: [
                    {
                        title: 'Authentification à Deux Facteurs',
                        description: 'Sécurisez votre compte avec une couche supplémentaire de protection. Votre sécurité est notre priorité.'
                    },
                    {
                        title: 'Code de Vérification',
                        description: 'Entrez le code à 6 chiffres généré par votre application d\'authentification pour confirmer votre identité.'
                    },
                    {
                        title: 'Codes de Récupération',
                        description: 'En cas de perte d\'accès, utilisez vos codes de récupération pour vous reconnecter à votre compte.'
                    },
                    {
                        title: 'Protection Maximale',
                        description: 'L\'authentification à deux facteurs protège votre compte même si votre mot de passe est compromis.'
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
                    <img class="w-full h-full object-cover opacity-20" src="/coleos-assets/nav/2.png" alt="Capocop">
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
