import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';

/**
 * Configuration Tailwind CSS pour Glotelho.cm
 * Design System basé sur l'analyse du site https://glotelho.cm/
 * 
 * Palette de couleurs :
 * - Primaire : Bleu très foncé (#000A2B) - moderne, tech
 * - Accent : Orange vif (#FF8A4C, #F97316) - énergique, e-commerce
 * - Success : Vert (#25D366) - WhatsApp green
 * - Info : Bleu (#1A56DB, #007AFF) - confiance
 * 
 * Typographie : Montserrat (principale)
 */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './vendor/laravel/jetstream/**/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './app/Filament/**/*.php',
        './resources/views/filament/**/*.blade.php',
        './vendor/filament/**/*.blade.php',
    ],

    theme: {
        extend: {
            // Typographie - Montserrat comme police principale
            fontFamily: {
                sans: ['Montserrat', ...defaultTheme.fontFamily.sans],
                heading: ['Montserrat', ...defaultTheme.fontFamily.sans],
                body: ['Montserrat', ...defaultTheme.fontFamily.sans],
            },

            // Breakpoints
            screens: {
                'xs': '480px',
                'sm': '640px',
                'md': '768px',
                'lg': '1024px',
                'xl': '1280px',
                '2xl': '1536px',
            },

            // Palette de couleurs Glotelho
            colors: {
                // Couleurs de base
                current: 'currentColor',
                transparent: 'transparent',
                black: '#000000',
                white: '#FFFFFF',

                // Couleur primaire - Bleu très foncé Glotelho
                primary: {
                    50: '#E6E8F0',
                    100: '#CCD1E1',
                    200: '#99A3C3',
                    300: '#6675A5',
                    400: '#334787',
                    500: '#000A2B', // Couleur principale
                    600: '#000822',
                    700: '#000619',
                    800: '#000411',
                    900: '#000208',
                    DEFAULT: '#000A2B',
                },

                // Orange - Couleur accent principale
                orange: {
                    50: '#FFF4ED',
                    100: '#FFE9DB',
                    200: '#FFD3B7',
                    300: '#FFBD93',
                    400: '#FFA76F',
                    500: '#FF8A4C', // Orange principal
                    600: '#FF7A2E',
                    700: '#F97316', // Orange vif
                    800: '#CC5D12',
                    900: '#99470D',
                    DEFAULT: '#FF8A4C',
                },

                // Orange vif alternatif
                'orange-bright': {
                    50: '#FFF7ED',
                    100: '#FFEFDB',
                    200: '#FFDFB7',
                    300: '#FFCF93',
                    400: '#FFBF6F',
                    500: '#F97316', // Orange vif
                    600: '#C75C12',
                    700: '#95450D',
                    800: '#632E09',
                    900: '#311704',
                },

                // Vert success (WhatsApp green)
                success: {
                    50: '#E6F9ED',
                    100: '#CCF3DB',
                    200: '#99E7B7',
                    300: '#66DB93',
                    400: '#33CF6F',
                    500: '#25D366', // Vert WhatsApp
                    600: '#1EA952',
                    700: '#177F3E',
                    800: '#0F5529',
                    900: '#082A15',
                    DEFAULT: '#25D366',
                },

                // Bleu info
                info: {
                    50: '#E6F0FF',
                    100: '#CCE1FF',
                    200: '#99C3FF',
                    300: '#66A5FF',
                    400: '#3387FF',
                    500: '#1A56DB', // Bleu principal
                    600: '#1565C0',
                    700: '#0D47A1',
                    800: '#092E6B',
                    900: '#041735',
                    DEFAULT: '#1A56DB',
                },

                // Bleu iOS
                'blue-ios': {
                    DEFAULT: '#007AFF',
                    50: '#E6F2FF',
                    100: '#CCE5FF',
                    500: '#007AFF',
                    600: '#0062CC',
                    700: '#004999',
                },

                // Jaune/Or
                gold: {
                    50: '#FEF9E7',
                    100: '#FDF3CF',
                    200: '#FBE79F',
                    300: '#F9DB6F',
                    400: '#F7CF3F',
                    500: '#C99F2F', // Or Glotelho
                    600: '#A17F26',
                    700: '#795F1C',
                    800: '#513F13',
                    900: '#291F09',
                },

                // Gris neutres
                gray: {
                    50: '#F1F5F9', // Fond clair
                    100: '#E2E8F0',
                    200: '#CBD5E1',
                    300: '#94A3B8',
                    400: '#64748B',
                    500: '#475569',
                    600: '#334155',
                    700: '#1E293B', // Texte foncé
                    800: '#0F172A',
                    900: '#030712',
                },

                // Couleurs d'état
                warning: {
                    DEFAULT: '#FFA500',
                    50: '#FFF8E6',
                    100: '#FFF1CC',
                    500: '#FFA500',
                    600: '#CC8400',
                },
                error: {
                    DEFAULT: '#FC7575',
                    50: '#FEF2F2',
                    100: '#FEE2E2',
                    500: '#FC7575',
                    600: '#C85E5E',
                },

                // Couleurs supplémentaires Glotelho
                purple: {
                    500: '#7A8EF7', // Violet clair
                },
                teal: {
                    500: '#5BC199', // Turquoise
                },
                pink: {
                    500: '#EB58B9', // Rose
                },
                cyan: {
                    500: '#7DD6F6', // Cyan clair
                },
            },

            // Espacements
            spacing: {
                '0': '0px',
                '1': '0.25rem',
                '2': '0.5rem',
                '3': '0.75rem',
                '4': '1rem',
                '5': '1.25rem',
                '6': '1.5rem',
                '7': '1.75rem',
                '8': '2rem',
                '9': '2.25rem',
                '10': '2.5rem',
                '12': '3rem',
                '16': '4rem',
                '20': '5rem',
                '24': '6rem',
                '32': '8rem',
                '40': '10rem',
                '48': '12rem',
                '64': '16rem',
                '72': '18rem',
                '80': '20rem',
                '96': '24rem',
            },

            // Bordures
            borderRadius: {
                'none': '0',
                'sm': '0.25rem',
                'DEFAULT': '0.5rem',
                'md': '0.75rem',
                'lg': '1rem',
                'xl': '1.5rem',
                '2xl': '2rem',
                '3xl': '3rem',
                'full': '9999px',
            },

            // Ombres - Design moderne et dynamique
            boxShadow: {
                'sm': '0 1px 2px 0 rgba(0, 10, 43, 0.05)',
                'DEFAULT': '0 1px 3px 0 rgba(0, 10, 43, 0.1), 0 1px 2px 0 rgba(0, 10, 43, 0.06)',
                'md': '0 4px 6px -1px rgba(0, 10, 43, 0.1), 0 2px 4px -1px rgba(0, 10, 43, 0.06)',
                'lg': '0 10px 15px -3px rgba(0, 10, 43, 0.1), 0 4px 6px -2px rgba(0, 10, 43, 0.05)',
                'xl': '0 20px 25px -5px rgba(0, 10, 43, 0.1), 0 10px 10px -5px rgba(0, 10, 43, 0.04)',
                '2xl': '0 25px 50px -12px rgba(0, 10, 43, 0.25)',
                'inner': 'inset 0 2px 4px 0 rgba(0, 10, 43, 0.06)',
                'none': 'none',
                // Ombres spécifiques Glotelho
                'glotelho': '0 4px 12px rgba(255, 138, 76, 0.2)',
                'glotelho-lg': '0 8px 24px rgba(0, 10, 43, 0.3)',
                'glotelho-orange': '0 4px 16px rgba(249, 115, 22, 0.25)',
            },

            // Dégradés
            backgroundImage: {
                'none': 'none',
                'gradient-to-t': 'linear-gradient(to top, var(--tw-gradient-stops))',
                'gradient-to-tr': 'linear-gradient(to top right, var(--tw-gradient-stops))',
                'gradient-to-r': 'linear-gradient(to right, var(--tw-gradient-stops))',
                'gradient-to-br': 'linear-gradient(to bottom right, var(--tw-gradient-stops))',
                'gradient-to-b': 'linear-gradient(to bottom, var(--tw-gradient-stops))',
                'gradient-to-bl': 'linear-gradient(to bottom left, var(--tw-gradient-stops))',
                'gradient-to-l': 'linear-gradient(to left, var(--tw-gradient-stops))',
                'gradient-to-tl': 'linear-gradient(to top left, var(--tw-gradient-stops))',
                // Dégradés Glotelho
                'glotelho-primary': 'linear-gradient(135deg, #000A2B 0%, #0F172A 100%)',
                'glotelho-orange': 'linear-gradient(135deg, #FF8A4C 0%, #F97316 100%)',
                'glotelho-hero': 'linear-gradient(135deg, #000A2B 0%, #1A56DB 100%)',
                'glotelho-vibrant': 'linear-gradient(135deg, #FF8A4C 0%, #25D366 50%, #1A56DB 100%)',
            },

            // Animations
            animation: {
                'none': 'none',
                'spin': 'spin 1s linear infinite',
                'ping': 'ping 1s cubic-bezier(0, 0, 0.2, 1) infinite',
                'pulse': 'pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                'bounce': 'bounce 1s infinite',
                'fade-in': 'fadeIn 0.5s ease-in',
                'slide-up': 'slideUp 0.5s ease-out',
                'scale-in': 'scaleIn 0.3s ease-out',
            },

            keyframes: {
                fadeIn: {
                    '0%': { opacity: '0' },
                    '100%': { opacity: '1' },
                },
                slideUp: {
                    '0%': { transform: 'translateY(20px)', opacity: '0' },
                    '100%': { transform: 'translateY(0)', opacity: '1' },
                },
                scaleIn: {
                    '0%': { transform: 'scale(0.95)', opacity: '0' },
                    '100%': { transform: 'scale(1)', opacity: '1' },
                },
            },

            // Tailles de police
            fontSize: {
                'xs': ['0.75rem', { lineHeight: '1.5' }],
                'sm': ['0.875rem', { lineHeight: '1.5' }],
                'base': ['1rem', { lineHeight: '1.5' }],
                'lg': ['1.125rem', { lineHeight: '1.5' }],
                'xl': ['1.25rem', { lineHeight: '1.4' }],
                '2xl': ['1.5rem', { lineHeight: '1.3' }],
                '3xl': ['1.875rem', { lineHeight: '1.3' }],
                '4xl': ['2.25rem', { lineHeight: '1.2' }],
                '5xl': ['3rem', { lineHeight: '1.25' }],
                '6xl': ['3.75rem', { lineHeight: '1.1' }],
            },

            // Poids de police
            fontWeight: {
                'light': '300',
                'normal': '400',
                'medium': '500',
                'semibold': '600',
                'bold': '700',
                'extrabold': '800',
            },
        },
    },

    plugins: [forms, typography],
};



