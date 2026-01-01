<x-guest-layout>
    <section data-section-id="1" data-share="" data-category="reset-password" data-component-id="fed93509_05_awz" class="relative overflow-hidden">
        <div class="relative flex flex-wrap">
            <div class="w-full lg:w-1/2 px-4">
                <div class="flex items-center justify-center w-full h-full">
                    <div class="max-w-md lg:max-w-lg pt-24 pb-20 lg:pt-20 mx-auto w-full">
                        <h2 class="text-rhino-700 text-2xl font-semibold mb-6 font-heading text-center">{{ __('auth.reset_password_title') }}</h2>

                        <x-validation-errors class="mb-4" />

                        <form method="POST" action="{{ route('password.update') }}">
                            @csrf

                            <input type="hidden" name="token" value="{{ $request->route('token') }}">

                            <div class="flex flex-col gap-1 mb-4">
                                <label class="text-coolGray-700 text-sm font-medium mb-2" for="email">{{ __('auth.email') }}</label>
                                <x-input id="email" type="email" name="email" :value="old('email', $request->email)" required autofocus autocomplete="username" :placeholder="__('auth.placeholder_email')" />
                                @error('email')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                            </div>

                            <div class="flex flex-col gap-1 mb-4">
                                <label class="text-coolGray-700 text-sm font-medium mb-2" for="password">{{ __('auth.new_password') }}</label>
                                <x-password-input id="password" name="password" required autocomplete="new-password" :placeholder="__('auth.placeholder_password')" />
                                @error('password')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                            </div>

                            <div class="flex flex-col gap-1 mb-6">
                                <label class="text-coolGray-700 text-sm font-medium mb-2" for="password_confirmation">{{ __('auth.confirm_password') }}</label>
                                <x-password-input id="password_confirmation" name="password_confirmation" required autocomplete="new-password" :placeholder="__('auth.placeholder_confirm_password')" />
                                @error('password_confirmation')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                            </div>

                            <x-button class="rounded-sm py-3 px-4 bg-purple-500 shadow-md text-white font-medium text-sm w-full hover:bg-purple-600 focus:outline-none focus:ring-2 focus:ring-purple-400 focus:ring-offset-2 transition duration-200">{{ __('auth.reset_password_button') }}</x-button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Section image avec textes qui swipent -->
            <div class="relative lg:absolute top-0 right-0 w-full lg:w-1/2 block h-112 lg:h-full overflow-hidden bg-gradient-to-br from-teal-600 via-emerald-600 to-green-700" x-data="{
                currentSlide: 0,
                autoplayInterval: null,
                slides: [
                    {
                        title: 'Nouveau Mot de Passe',
                        description: 'Choisissez un mot de passe fort et sécurisé pour protéger votre compte Capocop.'
                    },
                    {
                        title: 'Sécurité Renforcée',
                        description: 'Utilisez une combinaison de lettres, chiffres et caractères spéciaux pour une meilleure protection.'
                    },
                    {
                        title: 'Accès Récupéré',
                        description: 'Une fois votre mot de passe réinitialisé, vous pourrez accéder à tous vos services.'
                    },
                    {
                        title: 'Connexion Simplifiée',
                        description: 'Avec votre nouveau mot de passe, reconnectez-vous facilement à votre espace personnel.'
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
                    <img class="w-full h-full object-cover opacity-20" src="/coleos-assets/nav/3.png" alt="Capocop">
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
