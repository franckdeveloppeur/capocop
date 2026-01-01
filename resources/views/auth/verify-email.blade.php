<x-guest-layout>
    <section data-section-id="1" data-share="" data-category="verify-email" data-component-id="fed93509_05_awz" class="relative overflow-hidden">
        <div class="relative flex flex-wrap">
            <div class="w-full lg:w-1/2 px-4">
                <div class="flex items-center justify-center w-full h-full">
                    <div class="max-w-sm pt-24 pb-20 lg:pt-20 mx-auto">
                        <h2 class="text-rhino-700 text-2xl font-semibold mb-4 font-heading text-center">{{ __('auth.verify_email') }}</h2>
                        <p class="text-center text-sm text-coolGray-600 mb-6">{{ __('auth.verify_email_description') }}</p>

                        @if (session('status') == 'verification-link-sent')
                            <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-700 text-sm rounded-sm text-center">
                                {{ __('auth.verification_link_sent') }}
                            </div>
                        @endif

                        <form method="POST" action="{{ route('verification.send') }}" class="mb-4">
                            @csrf
                            <x-button class="rounded-sm py-3 px-4 bg-purple-500 shadow-md text-white font-medium text-sm w-full hover:bg-purple-600 focus:outline-none focus:ring-2 focus:ring-purple-400 focus:ring-offset-2 transition duration-200">{{ __('auth.resend_verification_email') }}</x-button>
                        </form>

                        <div class="flex items-center justify-center gap-4 text-sm">
                            <a href="{{ route('profile.show') }}" class="text-indigo-600 hover:underline font-medium">{{ __('auth.edit_profile') }}</a>
                            <span class="text-coolGray-300">|</span>
                            <form method="POST" action="{{ route('logout') }}" class="inline">
                                @csrf
                                <button type="submit" class="text-indigo-600 hover:underline font-medium">{{ __('auth.log_out') }}</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section image avec textes qui swipent -->
            <div class="relative lg:absolute top-0 right-0 w-full lg:w-1/2 block h-112 lg:h-full overflow-hidden bg-gradient-to-br from-indigo-600 via-indigo-700 to-purple-800" x-data="{
                currentSlide: 0,
                autoplayInterval: null,
                slides: [
                    {
                        title: 'Vérification Email',
                        description: 'Vérifiez votre adresse email pour activer toutes les fonctionnalités de votre compte Capocop.'
                    },
                    {
                        title: 'Sécurité Renforcée',
                        description: 'La vérification de votre email garantit la sécurité de votre compte et de vos données.'
                    },
                    {
                        title: 'Notifications Importantes',
                        description: 'Recevez les notifications importantes concernant vos commandes et vos paiements par email.'
                    },
                    {
                        title: 'Accès Complet',
                        description: 'Une fois votre email vérifié, vous aurez accès à toutes les fonctionnalités de votre espace.'
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
