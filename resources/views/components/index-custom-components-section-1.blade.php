<section class="relative overflow-hidden overflow-x-hidden" x-data="{
                       currentSlide: 0,
                       autoplayInterval: null,
                       slides: [
                       {
                       badge: 'Nouveau',
                       title: 'Achetez maintenant, Payez progressivement et à moindre coût',
                       description: 'Découvrez notre gamme complète de produits : bouteilles de gaz, plaques de cuisson, fournitures scolaires et bien plus. Profitez du paiement échelonné sur salaire pour faciliter vos achats.',
                       image: '/coleos-assets/nav/1.png',
                       bg: 'bg-gradient-to-br from-purple-600 via-purple-700 to-purple-800'
                       },
                       {
                       badge: 'Copay',
                       title: 'Devenez client à Capocop et menez une vie paisible.',
                        description: ' Profitez de nos facilités de paiement échelonné pour acquérir tous les produits dont vous avez besoin sans vous soucier.',
                        image: '/coleos-assets/nav/3.png',
                       bg: 'bg-gradient-to-br from-indigo-600 via-indigo-700 to-purple-800'
                       },
                       {
                       badge: 'Promo',
                       title: 'Fournitures Scolaires à Prix Réduits',
                       description: 'Équipez vos enfants pour la rentrée avec notre large sélection de fournitures scolaires. Paiement flexible adapté à votre budget.',
                       image: '/coleos-assets/nav/6.png',
                       bg: 'bg-gradient-to-br from-blue-600 via-purple-600 to-purple-700'
                       },
                       {
                       badge: 'Populaire',
                       title: 'Équipements de Cuisine Essentiels',
                       description: 'Plaques de cuisson, bouteilles de gaz et accessoires. Tout ce dont vous avez besoin pour votre cuisine, avec facilités de paiement.',
                       image: '/coleos-assets/nav/2.png',
                       bg: 'bg-gradient-to-br from-blue-600 via-purple-600 to-purple-700'
                       }
                       ],
                       startAutoplay() {
                       this.autoplayInterval = setInterval(() =&gt; {
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
                       },
                       goToSlide(index) {
                       this.currentSlide = index;
                       }
                       }" x-init="startAutoplay()" @mouseenter="stopAutoplay()" @mouseleave="startAutoplay()">
      
        <!-- Slides -->
        <div class="relative min-h-[400px] sm:min-h-[450px] md:min-h-[500px] lg:min-h-[600px] xl:min-h-[650px] overflow-hidden">
          <template x-for="(slide, index) in slides" :key="index">
            <div x-show="currentSlide === index" x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0 transform translate-x-full" x-transition:enter-end="opacity-100 transform translate-x-0" x-transition:leave="transition ease-in duration-500" x-transition:leave-start="opacity-100 transform translate-x-0" x-transition:leave-end="opacity-0 transform -translate-x-full" class="absolute inset-0 w-full max-w-full" :class="slide.bg">
      
              <!-- Version Mobile : Image en plein écran avec texte en bas -->
              <div class="lg:hidden relative h-full w-full flex items-center justify-center overflow-hidden">
                <img class="w-full h-full object-cover object-center" :src="slide.image" :alt="slide.title">
                
                <!-- Overlay gradient pour la lisibilité du texte -->
                <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/30 to-transparent"></div>
                
                <!-- Texte en bas -->
                <div class="absolute bottom-0 left-0 right-0 px-4 sm:px-6 pb-6 sm:pb-8 z-10">
                  <div class="max-w-full mx-auto text-center">
                    <span class="inline-block py-1.5 px-4 mb-3 text-xs sm:text-sm font-bold text-white bg-white/20 backdrop-blur-sm uppercase rounded-full tracking-wider shadow-lg" x-text="slide.badge"></span>
                    
                    <h1 class="text-2xl sm:text-3xl md:text-4xl font-bold text-white mb-3 sm:mb-4 leading-tight px-2" x-text="slide.title"></h1>
                    
                    <p class="text-base sm:text-lg text-white/90 mb-4 sm:mb-5 leading-relaxed line-clamp-2 px-2" x-text="slide.description"></p>
                    
                    <div class="flex flex-col gap-2.5 sm:gap-3 justify-center">
                      <a class="inline-block px-5 sm:px-6 py-2.5 sm:py-3 rounded-lg text-center text-sm sm:text-base font-semibold text-purple-700 bg-white hover:bg-purple-50 transition duration-300 shadow-lg whitespace-nowrap" href="#produits">
                        Explorer les produits
                      </a>
                      <a class="inline-block px-5 sm:px-6 py-2.5 sm:py-3 rounded-lg border-2 border-white text-center text-sm sm:text-base font-semibold text-white hover:bg-white hover:text-purple-700 transition duration-300 shadow-lg whitespace-nowrap" href="#paiement">
                        Comment ça marche
                      </a>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Version Desktop : Disposition originale -->
              <div class="hidden lg:block relative h-full px-4 md:px-6 lg:px-8 py-8 md:py-12 lg:py-16 xl:py-20 overflow-hidden">
                <div class="max-w-7xl mx-auto h-full w-full">
                  <div class="flex flex-row items-center justify-between h-full gap-8">
      
                    <!-- Contenu texte -->
                    <div class="w-full lg:w-1/2 text-left flex-shrink-0">
                      <div class="max-w-lg">
                        <span class="inline-block py-1.5 px-4 mb-4 text-xs font-bold text-white bg-white/20 backdrop-blur-sm uppercase rounded-full tracking-wider shadow-lg" x-text="slide.badge"></span>
      
                        <h1 class="text-3xl md:text-4xl lg:text-5xl xl:text-6xl font-bold text-white mb-5 leading-tight" x-text="slide.title"></h1>
      
                        <p class="text-base md:text-lg text-purple-50 mb-8 leading-relaxed" x-text="slide.description"></p>
      
                        <div class="flex flex-wrap gap-3">
                          <a class="inline-block px-6 py-3 rounded-lg text-center text-sm font-semibold text-purple-700 bg-white hover:bg-purple-50 transition duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5" href="#produits">
                            Explorer les produits
                          </a>
                          <a class="inline-block px-6 py-3 rounded-lg border-2 border-white text-center text-sm font-semibold text-white hover:bg-white hover:text-purple-700 transition duration-300 shadow-lg" href="#paiement">
                            Comment ça marche
                          </a>
                        </div>
                      </div>
                    </div>
      
                    <!-- Image -->
                    <div class="w-full lg:w-1/2 flex-shrink-0">
                      <div class="relative flex items-center justify-center h-full">
                        <img class="w-full h-auto max-h-[400px] lg:max-h-[450px] xl:max-h-96 object-contain drop-shadow-2xl" :src="slide.image" :alt="slide.title">
                      </div>
                    </div>
      
                  </div>
                </div>
              </div>
            </div>
          </template>
        </div>
      
        <!-- Navigation Arrows -->
        <button @click="prevSlide()" class="nav-arrow absolute left-1 sm:left-2 md:left-4 top-1/2 -translate-y-1/2 w-8 h-8 sm:w-10 sm:h-10 md:w-12 md:h-12 rounded-full bg-white/10 hover:bg-white/20 active:bg-white/30 flex items-center justify-center text-white z-10 border border-white/30 transition-all duration-300 hover:scale-110 active:scale-95 touch-manipulation">
          <svg class="w-4 h-4 sm:w-5 sm:h-5 md:w-6 md:h-6" fill="none" stroke="currentColor" viewbox="0 0 24 24" data-config-id="svg-inline1">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path>
          </svg>
        </button>
      
        <button @click="nextSlide()" class="nav-arrow absolute right-1 sm:right-2 md:right-4 top-1/2 -translate-y-1/2 w-8 h-8 sm:w-10 sm:h-10 md:w-12 md:h-12 rounded-full bg-white/10 hover:bg-white/20 active:bg-white/30 flex items-center justify-center text-white z-10 border border-white/30 transition-all duration-300 hover:scale-110 active:scale-95 touch-manipulation">
          <svg class="w-4 h-4 sm:w-5 sm:h-5 md:w-6 md:h-6" fill="none" stroke="currentColor" viewbox="0 0 24 24" data-config-id="svg-inline2">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path>
          </svg>
        </button>
      
        <!-- Navigation Dots avec Pagination Styl&eacute;e -->
        <div class="absolute bottom-2 sm:bottom-4 md:bottom-6 lg:bottom-8 left-1/2 -translate-x-1/2 z-10 w-full px-2 sm:px-3 md:px-4 max-w-full overflow-hidden">
          <div class="flex items-center justify-center gap-1.5 sm:gap-2 md:gap-3 lg:gap-4 bg-white/10 backdrop-blur-md rounded-full px-2 sm:px-3 md:px-4 lg:px-6 py-1.5 sm:py-2 md:py-2.5 lg:py-3 border border-white/20 shadow-2xl max-w-fit mx-auto">
      
            <!-- Bouton Pr&eacute;c&eacute;dent Mini -->
            <button @click="prevSlide()" class="w-6 h-6 sm:w-7 sm:h-7 md:w-8 md:h-8 rounded-full bg-white/20 hover:bg-white/30 active:bg-white/40 flex items-center justify-center text-white transition-all duration-300 hover:scale-110 active:scale-95 flex-shrink-0 touch-manipulation">
              <svg class="w-3 h-3 sm:w-3.5 sm:h-3.5 md:w-4 md:h-4" fill="none" stroke="currentColor" viewbox="0 0 24 24" data-config-id="svg-inline3">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path>
              </svg>
            </button>
      
            <!-- Dots avec num&eacute;rotation -->
            <div class="flex gap-1 sm:gap-1.5 md:gap-2 items-center">
              <template x-for="(slide, index) in slides" :key="index">
                <button @click="goToSlide(index)" class="group relative flex items-center justify-center transition-all duration-300 touch-manipulation" :aria-label="'Aller à la slide ' + (index + 1)">
      
                  <!-- Dot actif agrandi avec num&eacute;ro -->
                  <div x-show="currentSlide === index" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-50" x-transition:enter-end="opacity-100 scale-100" class="w-7 h-7 sm:w-8 sm:h-8 md:w-9 md:h-9 lg:w-10 lg:h-10 rounded-full bg-white flex items-center justify-center shadow-lg">
                    <span class="text-purple-700 font-bold text-[10px] sm:text-xs md:text-sm" x-text="index + 1"></span>
      
                    <!-- Cercle de progression -->
                    <svg class="absolute inset-0 w-7 h-7 sm:w-8 sm:h-8 md:w-9 md:h-9 lg:w-10 lg:h-10 -rotate-90" viewbox="0 0 40 40">
                      <circle cx="20" cy="20" r="18" fill="none" stroke="rgba(147, 51, 234, 0.2)" stroke-width="2"></circle>
                      <circle cx="20" cy="20" r="18" fill="none" stroke="rgb(147, 51, 234)" stroke-width="2" class="animate-progress-circle" stroke-dasharray="113" stroke-dashoffset="0" stroke-linecap="round"></circle>
                    </svg>
                  </div>
      
                  <!-- Dot inactif simple -->
                  <div x-show="currentSlide !== index" class="w-2 h-2 sm:w-2.5 sm:h-2.5 md:w-3 md:h-3 rounded-full bg-white/40 hover:bg-white/70 active:bg-white/90 transition-all duration-300 hover:scale-125">
                  </div>
      
                  <!-- Tooltip au survol (masqué sur mobile) -->
                  <div class="hidden md:block absolute -top-10 left-1/2 -translate-x-1/2 opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none">
                    <div class="bg-white text-purple-700 text-xs font-semibold px-3 py-1.5 rounded-lg shadow-lg whitespace-nowrap">
                      <span x-text="slide.badge"></span>
                    </div>
                    <div class="w-2 h-2 bg-white rotate-45 absolute -bottom-1 left-1/2 -translate-x-1/2"></div>
                  </div>
                </button>
              </template>
            </div>
      
            <!-- Bouton Suivant Mini -->
            <button @click="nextSlide()" class="w-6 h-6 sm:w-7 sm:h-7 md:w-8 md:h-8 rounded-full bg-white/20 hover:bg-white/30 active:bg-white/40 flex items-center justify-center text-white transition-all duration-300 hover:scale-110 active:scale-95 flex-shrink-0 touch-manipulation">
              <svg class="w-3 h-3 sm:w-3.5 sm:h-3.5 md:w-4 md:h-4" fill="none" stroke="currentColor" viewbox="0 0 24 24" data-config-id="svg-inline4">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path>
              </svg>
            </button>
      
            <!-- Compteur de slides -->
            <div class="ml-1 sm:ml-2 pl-1.5 sm:pl-2 md:pl-3 border-l border-white/30 hidden sm:block">
              <span class="text-white font-semibold text-[10px] sm:text-xs md:text-sm">
                <span x-text="currentSlide + 1"></span>/<span x-text="slides.length"></span>
              </span>
            </div>
      
          </div>
        </div>
      
        <style>
          @keyframes progress {
            from { width: 0%; }
            to { width: 100%; }
          }
          .animate-progress {
            animation: progress 5s linear;
          }
        </style>
        
        
      
      </section>
                  