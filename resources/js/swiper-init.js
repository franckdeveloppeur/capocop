// Swiper initialization for featured products
import Swiper from 'swiper';
import { Navigation, Pagination, Autoplay, EffectFade, Parallax } from 'swiper/modules';

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    // Initialize main products Swiper (Coverflow effect)
    const productsSwiperEl = document.getElementById('productsSwiper');
    if (productsSwiperEl) {
        const productsSwiper = new Swiper('#productsSwiper', {
            modules: [Navigation, Pagination, Autoplay],
            grabCursor: true,
            centeredSlides: false,
            slidesPerView: 1.2,
            spaceBetween: 12,
            loop: false,
            initialSlide: 0,
            watchSlidesProgress: true,
            freeMode: false,
            autoplay: {
                delay: 4000,
                disableOnInteraction: false,
                pauseOnMouseEnter: true,
            },
            breakpoints: {
                320: {
                    slidesPerView: 1.2,
                    spaceBetween: 12,
                },
                480: {
                    slidesPerView: 1.8,
                    spaceBetween: 14,
                },
                640: {
                    slidesPerView: 2.2,
                    spaceBetween: 16,
                },
                768: {
                    slidesPerView: 3,
                    spaceBetween: 18,
                },
                1024: {
                    slidesPerView: 4,
                    spaceBetween: 20,
                },
                1280: {
                    slidesPerView: 4,
                    spaceBetween: 24,
                },
                1536: {
                    slidesPerView: 5,
                    spaceBetween: 24,
                },
            },
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
                dynamicBullets: true,
            },
            on: {
                beforeInit: function() {
                    // Ensure container width is set before initialization
                    const container = this.el;
                    if (container) {
                        container.style.width = '100%';
                        container.style.maxWidth = '100%';
                    }
                },
                init: function() {
                    const container = this.el;
                    
                    // Force container to full width
                    if (container) {
                        container.style.width = '100%';
                        container.style.maxWidth = '100%';
                    }
                    
                    // Force update and ensure we start at slide 0
                    this.update();
                    this.updateSlides();
                    this.slideTo(0, 0);
                    
                    // Recalculate after a short delay to ensure DOM is ready
                    setTimeout(() => {
                        const containerWidth = container.offsetWidth || container.clientWidth;
                        const slidesPerView = this.params.slidesPerView;
                        const spaceBetween = this.params.spaceBetween;
                        
                        if (containerWidth && slidesPerView && typeof slidesPerView === 'number') {
                            // Calculate slide width
                            const totalSpaces = (slidesPerView - 1) * spaceBetween;
                            const slideWidth = (containerWidth - totalSpaces) / slidesPerView;
                            
                            // Apply width to all slides
                            this.slides.forEach((slide) => {
                                slide.style.width = slideWidth + 'px';
                                slide.style.minWidth = slideWidth + 'px';
                            });
                        }
                        
                        this.update();
                        this.updateSlides();
                        this.slideTo(0, 0);
                    }, 150);
                },
                resize: function() {
                    this.update();
                    this.updateSlides();
                },
            },
        });
    }

    // Initialize product images Swipers (Fade effect)
    document.querySelectorAll('[class*="product-images-"]').forEach((el) => {
        const swiperClass = Array.from(el.classList).find(cls => cls.startsWith('product-images-'));
        if (swiperClass && el.querySelectorAll('.swiper-slide').length > 1) {
            const index = swiperClass.replace('product-images-', '');
            const productImagesSwiper = new Swiper(`.${swiperClass}`, {
                modules: [Pagination, Autoplay, EffectFade],
                effect: 'fade',
                fadeEffect: {
                    crossFade: true,
                },
                autoplay: {
                    delay: 3000,
                    disableOnInteraction: false,
                    pauseOnMouseEnter: true,
                },
                pagination: {
                    el: `.product-images-pagination-${index}`,
                    clickable: true,
                    dynamicBullets: true,
                },
                loop: true,
                speed: 600,
            });

            // Pause autoplay on hover
            const card = el.closest('.product-card');
            if (card) {
                card.addEventListener('mouseenter', () => {
                    productImagesSwiper.autoplay.stop();
                });
                card.addEventListener('mouseleave', () => {
                    productImagesSwiper.autoplay.start();
                });
            }
        }
    });

});

