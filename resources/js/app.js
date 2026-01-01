import './bootstrap';
import.meta.glob([
    '!../**/(.*)',
	'!../**/(*.MD|*.md)',
    '../js/**',
    '../css/**',
    '../shuffle-for-tailwind.png',
    '../coleos-assets/**',
]);

// Import Swiper styles
import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/pagination';
import 'swiper/css/effect-coverflow';
import 'swiper/css/effect-fade';
import 'swiper/css/autoplay';
import 'swiper/css/parallax';

// Import Swiper initialization
import './swiper-init';