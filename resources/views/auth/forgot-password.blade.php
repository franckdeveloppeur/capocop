<x-guest-layout>
    <section data-section-id="1" data-share="" data-category="forgot-password" data-component-id="fed93509_05_awz" class="relative overflow-hidden">
        <div class="relative flex flex-wrap">
            <div class="w-full lg:w-1/2 px-4">
                <div class="flex items-center justify-center w-full h-full">
                    <div class="max-w-sm pt-24 pb-20 lg:pt-20 mx-auto">
                        <h2 class="text-rhino-700 text-2xl font-semibold mb-4 font-heading text-center">{{ __('auth.forgot_password_title') }}</h2>
                        <p class="text-center text-sm text-coolGray-600 mb-6">{{ __('auth.forgot_password_description') }}</p>

                        @if (session('status'))
                            <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-700 text-sm rounded-sm">{{ __('auth.reset_link_sent') }}</div>
                        @endif

                        <x-validation-errors class="mb-4" />

                        <form method="POST" action="{{ route('password.email') }}">
                            @csrf

                            <div class="flex flex-col gap-1 mb-6">
                                <label class="text-coolGray-700 text-sm font-medium mb-2" for="email">{{ __('auth.email') }}</label>
                                <x-input id="email" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" :placeholder="__('auth.placeholder_email')" />
                                @error('email')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                            </div>

                            <x-button class="rounded-sm py-3 px-4 bg-purple-500 shadow-md text-white font-medium text-sm w-full hover:bg-purple-600 focus:outline-none focus:ring-2 focus:ring-purple-400 focus:ring-offset-2 transition duration-200 mb-4">{{ __('auth.send_reset_link') }}</x-button>

                            <p class="text-center text-sm text-coolGray-700 mt-4">
                                <a href="{{ route('login') }}" class="text-indigo-600 hover:underline font-medium">{{ __('auth.back_to_login') }}</a>
                            </p>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Section image avec textes qui swipent -->
            <div class="relative lg:absolute top-0 right-0 w-full lg:w-1/2 block h-112 lg:h-full overflow-hidden bg-gradient-to-br from-blue-600 via-purple-600 to-purple-700" x-data="{
                currentSlide: 0,
                autoplayInterval: null,
                slides: [
                    {
                        title: 'Récupération de Mot de Passe',
                        description: 'Pas de souci ! Nous vous aiderons à récupérer l\'accès à votre compte en toute sécurité.'
                    },
                    {
                        title: 'Processus Simple',
                        description: 'Entrez votre email et recevez un lien de réinitialisation. C\'est rapide et sécurisé.'
                    },
                    {
                        title: 'Sécurité Garantie',
                        description: 'Vos données sont protégées. Le lien de réinitialisation est valide pendant une durée limitée.'
                    },
                    {
                        title: 'Besoin d\'Aide ?',
                        description: 'Notre équipe support est disponible pour vous assister dans la récupération de votre compte.'
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
