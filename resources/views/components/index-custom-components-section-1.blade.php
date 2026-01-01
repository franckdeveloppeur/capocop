<section class="relative overflow-hidden w-full max-w-full overflow-x-hidden" x-data="{
                       currentSlide: 0,
                       autoplayInterval: null,
                       slides: [
                       {
                       badge: 'Nouveau',
                       title: 'Achetez maintenant, Payez progressivement',
                       description: 'Découvrez notre gamme complète de produits : bouteilles de gaz, plaques de cuisson, fournitures scolaires et bien plus. Profitez du paiement échelonné sur salaire pour faciliter vos achats.',
                       image: '/coleos-assets/nav/1.png',
                       bg: 'bg-gradient-to-br from-purple-600 via-purple-700 to-purple-800'
                       },
                       {
                       badge: 'Promo',
                       title: 'Fournitures Scolaires à Prix Réduits',
                       description: 'Équipez vos enfants pour la rentrée avec notre large sélection de fournitures scolaires. Paiement flexible adapté à votre budget.',
                       image: '/coleos-assets/nav/6.png',
                       bg: 'bg-gradient-to-br from-indigo-600 via-indigo-700 to-purple-800'
                       },
                       {
                       badge: 'Populaire',
                       title: 'Équipements de Cuisine Essentiels',
                       description: 'Plaques de cuisson, bouteilles de gaz et accessoires. Tout ce dont vous avez besoin pour votre cuisine, avec facilités de paiement.',
                       image: '/coleos-assets/nav/2.png',
                       bg: 'bg-gradient-to-br from-blue-600 via-purple-600 to-purple-700'
                       },
                       {
                       badge: 'Capocop',
                       title: 'Devenez client Capocop et menez une vie paisible',
                       description: 'Rejoignez la communauté Capocop et profitez d\'une expérience d\'achat sereine. Qualité garantie, service client exceptionnel et solutions adaptées à vos besoins quotidiens.',
                       image: '/coleos-assets/nav/3.png',
                       bg: 'bg-gradient-to-br from-green-600 via-emerald-600 to-teal-700'
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
        <div class="relative min-h-[500px] md:min-h-[600px] w-full overflow-hidden overflow-x-hidden">
          <template x-for="(slide, index) in slides" :key="index">
            <div x-show="currentSlide === index" x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0 transform translate-x-full" x-transition:enter-end="opacity-100 transform translate-x-0" x-transition:leave="transition ease-in duration-500" x-transition:leave-start="opacity-100 transform translate-x-0" x-transition:leave-end="opacity-0 transform -translate-x-full" class="absolute inset-0 w-full max-w-full overflow-hidden" :class="slide.bg">
      
              <!-- Desktop Layout (lg et plus) -->
              <div class="hidden lg:block relative h-full px-4 py-16 md:py-20 lg:py-24 w-full max-w-full overflow-hidden overflow-x-hidden">
                <div class="max-w-7xl mx-auto h-full w-full overflow-hidden">
                  <div class="flex flex-wrap items-center h-full -mx-4 w-full overflow-hidden">
      
                    <!-- Contenu texte -->
                    <div class="w-full lg:w-1/2 px-4 mb-8 lg:mb-0">
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
                    <div class="w-full lg:w-1/2 px-4">
                      <div class="relative">
                        <img class="w-full h-auto max-h-96 object-contain drop-shadow-2xl" :src="slide.image" :alt="slide.title">
                      </div>
                    </div>
      
                  </div>
                </div>
              </div>

              <!-- Mobile Layout (moins de lg) -->
              <div class="lg:hidden relative h-full w-full overflow-hidden overflow-x-hidden">
                <!-- Image en plein écran -->
                <div class="absolute inset-0 w-full h-full overflow-hidden">
                  <img class="w-full h-full object-cover object-center parallax-bg-image" :src="slide.image" :alt="slide.title" style="max-width: 100%; height: 100%;">
                  <!-- Overlay dégradé pour la lisibilité du texte -->
                  <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/40 to-transparent"></div>
                </div>

                <!-- Contenu overlay en bas -->
                <div class="absolute bottom-0 left-0 right-0 px-4 pb-28 sm:pb-32 md:pb-36 pt-12 z-10 parallax-content">
                  <div class="max-w-lg mx-auto">
                    <!-- Badge -->
                    <span class="inline-block py-1.5 px-4 mb-2 sm:mb-3 text-xs font-bold text-white bg-white/20 backdrop-blur-sm uppercase rounded-full tracking-wider shadow-lg parallax-item-1" x-text="slide.badge"></span>
                    
                    <!-- Titre -->
                    <h1 class="text-xl sm:text-2xl md:text-3xl font-bold text-white mb-3 sm:mb-4 leading-tight drop-shadow-lg parallax-item-2 line-clamp-2" x-text="slide.title"></h1>
                    
                    <!-- Boutons d'action -->
                    <div class="flex flex-wrap gap-2 mt-3 sm:mt-4 parallax-item-3">
                      <a class="inline-block flex-1 px-3 sm:px-4 py-2 sm:py-2.5 rounded-lg text-center text-xs font-semibold text-purple-700 bg-white hover:bg-purple-50 transition duration-300 shadow-lg" href="#produits">
                        Explorer
                      </a>
                      <a class="inline-block flex-1 px-3 sm:px-4 py-2 sm:py-2.5 rounded-lg border-2 border-white text-center text-xs font-semibold text-white hover:bg-white/20 transition duration-300 shadow-lg" href="#paiement">
                        En savoir plus
                      </a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </template>
        </div>
      
        <!-- Navigation Arrows (Desktop uniquement) -->
        <button @click="prevSlide()" class="hidden lg:flex nav-arrow absolute left-4 top-1/2 -translate-y-1/2 w-12 h-12 rounded-full bg-white/10 hover:bg-white/20 items-center justify-center text-white z-10 border border-white/30">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewbox="0 0 24 24" data-config-id="svg-inline1">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path>
          </svg>
        </button>
      
        <button @click="nextSlide()" class="hidden lg:flex nav-arrow absolute right-4 top-1/2 -translate-y-1/2 w-12 h-12 rounded-full bg-white/10 hover:bg-white/20 items-center justify-center text-white z-10 border border-white/30">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewbox="0 0 24 24" data-config-id="svg-inline2">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path>
          </svg>
        </button>
      
        <!-- Navigation Dots avec Pagination Styl&eacute;e -->
        <div class="absolute bottom-2 sm:bottom-4 md:bottom-8 left-1/2 -translate-x-1/2 z-20">
          <div class="flex items-center gap-2 md:gap-4 bg-white/10 backdrop-blur-md rounded-full px-3 md:px-6 py-2 md:py-3 border border-white/20 shadow-2xl">
      
            <!-- Bouton Pr&eacute;c&eacute;dent Mini -->
            <button @click="prevSlide()" class="w-7 h-7 md:w-8 md:h-8 rounded-full bg-white/20 hover:bg-white/30 flex items-center justify-center text-white transition-all duration-300 hover:scale-110">
              <svg class="w-3.5 h-3.5 md:w-4 md:h-4" fill="none" stroke="currentColor" viewbox="0 0 24 24" data-config-id="svg-inline3">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path>
              </svg>
            </button>
      
            <!-- Dots avec num&eacute;rotation -->
            <div class="flex gap-2 items-center">
              <template x-for="(slide, index) in slides" :key="index">
                <button @click="goToSlide(index)" class="group relative flex items-center justify-center transition-all duration-300" :aria-label="'Aller à la slide ' + (index + 1)">
      
                  <!-- Dot actif agrandi avec num&eacute;ro -->
                  <div x-show="currentSlide === index" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-50" x-transition:enter-end="opacity-100 scale-100" class="w-8 h-8 md:w-10 md:h-10 rounded-full bg-white flex items-center justify-center shadow-lg">
                    <span class="text-purple-700 font-bold text-xs md:text-sm" x-text="index + 1"></span>
      
                    <!-- Cercle de progression -->
                    <svg class="absolute inset-0 w-8 h-8 md:w-10 md:h-10 -rotate-90" viewbox="0 0 40 40">
                      <circle cx="20" cy="20" r="18" fill="none" stroke="rgba(147, 51, 234, 0.2)" stroke-width="2"></circle>
                      <circle cx="20" cy="20" r="18" fill="none" stroke="rgb(147, 51, 234)" stroke-width="2" class="animate-progress-circle" stroke-dasharray="113" stroke-dashoffset="0" stroke-linecap="round"></circle>
                    </svg>
                  </div>
      
                  <!-- Dot inactif simple -->
                  <div x-show="currentSlide !== index" class="w-2.5 h-2.5 md:w-3 md:h-3 rounded-full bg-white/40 hover:bg-white/70 transition-all duration-300 hover:scale-125">
                  </div>
      
                  <!-- Tooltip au survol -->
                  <div class="absolute -top-10 left-1/2 -translate-x-1/2 opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none">
                    <div class="bg-white text-purple-700 text-xs font-semibold px-3 py-1.5 rounded-lg shadow-lg whitespace-nowrap">
                      <span x-text="slide.badge"></span>
                    </div>
                    <div class="w-2 h-2 bg-white rotate-45 absolute -bottom-1 left-1/2 -translate-x-1/2"></div>
                  </div>
                </button>
              </template>
            </div>
      
            <!-- Bouton Suivant Mini -->
            <button @click="nextSlide()" class="w-7 h-7 md:w-8 md:h-8 rounded-full bg-white/20 hover:bg-white/30 flex items-center justify-center text-white transition-all duration-300 hover:scale-110">
              <svg class="w-3.5 h-3.5 md:w-4 md:h-4" fill="none" stroke="currentColor" viewbox="0 0 24 24" data-config-id="svg-inline4">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path>
              </svg>
            </button>
      
            <!-- Compteur de slides -->
            <div class="ml-1 md:ml-2 pl-2 md:pl-3 border-l border-white/30">
              <span class="text-white font-semibold text-xs md:text-sm">
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
          /* Empêcher le scroll horizontal */
          section {
            max-width: 100vw;
            overflow-x: hidden !important;
          }
          section > * {
            max-width: 100%;
            overflow-x: hidden;
          }
          
          /* Effet parallax mobile - Simple et compatible avec Alpine.js */
          @media (max-width: 1023px) {
            .parallax-bg-image {
              transform: scale(1.1);
              transition: transform 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            }
            
            /* Effet parallax léger sur l'image lors du scroll ou changement */
            .parallax-content {
              transition: transform 0.6s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            }
            
            /* Animation d'entrée pour les éléments de contenu */
            .parallax-item-1 {
              transition: opacity 0.6s ease-out 0.1s, transform 0.6s ease-out 0.1s;
            }
            
            .parallax-item-2 {
              transition: opacity 0.8s ease-out 0.2s, transform 0.8s ease-out 0.2s;
            }
            
            .parallax-item-3 {
              transition: opacity 1s ease-out 0.3s, transform 1s ease-out 0.3s;
            }
            
            /* S'assurer que les boutons sont toujours visibles */
            .parallax-content {
              min-height: auto;
              max-height: 60vh;
              overflow: visible;
            }
            
            /* Ajuster la hauteur minimale du conteneur pour les slides avec plus de contenu */
            .parallax-item-2 {
              max-height: 4.5rem;
              overflow: hidden;
            }
          }
        </style>
        
        
      
      </section>
                  