import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './vendor/laravel/jetstream/**/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],
    // preset 
    

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            "screens": {
                "xs": "480px",
                "sm": "640px",
                "md": "768px",
                "lg": "1024px",
                "xl": "1280px"
            },
            "colors": {
                "current": "currentColor",
                "transparent": "transparent",
                "black": "#000",
                "white": "#fff",
                "coolGray": {
                    "50": "#F9FAFB",
                    "100": "#F3F4F6",
                    "200": "#E5E7EB",
                    "300": "#D1D5DB",
                    "400": "#9CA3AF",
                    "500": "#6B7280",
                    "600": "#4B5563",
                    "700": "#374151",
                    "800": "#1F2937",
                    "900": "#111827"
                },
                "gray": {
                    "50": "#F9F9F9",
                    "100": "#F2F2F2",
                    "200": "#DFDFDF",
                    "300": "#CCCCCC",
                    "400": "#AFAFAF",
                    "500": "#808080",
                    "600": "#737373",
                    "700": "#606060",
                    "800": "#4D4D4D",
                    "900": "#3F3F3F"
                },
                "rhino": {
                    "50": "#F5F5F7",
                    "100": "#E8EBF0",
                    "200": "#D3D6E0",
                    "300": "#A0A5B8",
                    "400": "#6F7792",
                    "500": "#313D63",
                    "600": "#2C3759",
                    "700": "#252E4A",
                    "800": "#1D253B",
                    "900": "#161C31"
                },
                "red": {
                    "50": "#FEF2F2",
                    "100": "#FEE2E2",
                    "200": "#FECACA",
                    "300": "#FCA5A5",
                    "400": "#F87171",
                    "500": "#EF4444",
                    "600": "#DC2626",
                    "700": "#B91C1C",
                    "800": "#991B1B",
                    "900": "#7F1D1D"
                },
                "yellow": {
                    "50": "#FEFCE8",
                    "100": "#FEF9C3",
                    "200": "#FEF08A",
                    "300": "#FDE047",
                    "400": "#FACC15",
                    "500": "#EAB308",
                    "600": "#CA8A04",
                    "700": "#A16207",
                    "800": "#854D0E",
                    "900": "#713F12"
                },
                "green": {
                    "50": "#F0FDF4",
                    "100": "#DCFCE7",
                    "200": "#BBF7D0",
                    "300": "#86EFAC",
                    "400": "#4ADE80",
                    "500": "#22C55E",
                    "600": "#16A34A",
                    "700": "#15803D",
                    "800": "#166534",
                    "900": "#14532D"
                },
                "blue": {
                    "50": "#EFF6FF",
                    "100": "#DBEAFE",
                    "200": "#BFDBFE",
                    "300": "#93C5FD",
                    "400": "#60A5FA",
                    "500": "#3B82F6",
                    "600": "#2563EB",
                    "700": "#1D4ED8",
                    "800": "#1E40AF",
                    "900": "#1E3A8A"
                },
                "coolBlue": {
                    "50": "#f4f9fb",
                    "100": "#e7f3f7",
                    "200": "#c0e1ea",
                    "300": "#9bd1de",
                    "400": "#65b9cb",
                    "500": "#41a0b6",
                    "600": "#308399",
                    "700": "#28697c",
                    "800": "#255967",
                    "900": "#234b57"
                },
                "indigo": {
                    "50": "#EEF2FF",
                    "100": "#E0E7FF",
                    "200": "#b2b8cd",
                    "300": "#A5B4FC",
                    "400": "#818CF8",
                    "500": "#6366F1",
                    "600": "#4F46E5",
                    "700": "#4338CA",
                    "800": "#3730A3",
                    "900": "#312E81"
                },
                "purple": {
                    "50": "#F8F8FF",
                    "100": "#F1F1FE",
                    "200": "#DDDCFE",
                    "300": "#C8C7FD",
                    "400": "#9E9DFB",
                    "500": "#7573F9",
                    "600": "#6968E0",
                    "700": "#5856BB",
                    "800": "#464595",
                    "900": "#39387A"
                },
                "coolPink": {
                    "50": "#fdf3f5",
                    "100": "#fce7eb",
                    "200": "#f8d3db",
                    "300": "#f2afbd",
                    "400": "#ed98ab",
                    "500": "#dd5678",
                    "600": "#c83661",
                    "700": "#a82851",
                    "800": "#8d2449",
                    "900": "#792244"
                },
                "pink": {
                    "50": "#FDF2F8",
                    "100": "#FCE7F3",
                    "200": "#FBCFE8",
                    "300": "#F9A8D4",
                    "400": "#F472B6",
                    "500": "#EC4899",
                    "600": "#DB2777",
                    "700": "#BE185D",
                    "800": "#9D174D",
                    "900": "#831843"
                },
                "orange": {
                    "50": "#FFF7F4",
                    "100": "#FFEEE9",
                    "200": "#FED5C8",
                    "300": "#FDBBA7",
                    "400": "#FC8964",
                    "500": "#FB5622",
                    "600": "#E24D1F",
                    "700": "#BC411A",
                    "800": "#973414",
                    "900": "#7B2A11"
                }
            },
            "spacing": {
                "0": "0px",
                "1": "0.25rem",
                "2": "0.5rem",
                "3": "0.75rem",
                "4": "1rem",
                "5": "1.25rem",
                "6": "1.5rem",
                "7": "1.75rem",
                "8": "2rem",
                "9": "2.25rem",
                "10": "2.5rem",
                "11": "2.75rem",
                "12": "3rem",
                "14": "3.5rem",
                "16": "4rem",
                "18": "4.5rem",
                "20": "5rem",
                "24": "6rem",
                "26": "6.5rem",
                "28": "7rem",
                "32": "8rem",
                "36": "9rem",
                "40": "10rem",
                "44": "11rem",
                "48": "12rem",
                "52": "13rem",
                "56": "14rem",
                "60": "15rem",
                "64": "16rem",
                "72": "18rem",
                "74": "18.5rem",
                "80": "20rem",
                "96": "24rem",
                "100": "25rem",
                "112": "28rem",
                "116": "29rem",
                "128": "32rem",
                "135": "33.75rem",
                "148": "37rem",
                "160": "40rem",
                "170": "42.5rem",
                "186": "46.5rem",
                "235": "58.75rem",
                "470": "117.5rem",
                "px": "1px",
                "0.5": "0.125rem",
                "1.5": "0.375rem",
                "2.5": "0.625rem",
                "3.5": "0.875rem"
            },
            "animation": {
                "none": "none",
                "spin": "spin 1s linear infinite",
                "ping": "ping 1s cubic-bezier(0, 0, 0.2, 1) infinite",
                "pulse": "pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite",
                "bounce": "bounce 1s infinite"
            },
            "backgroundColor": {
                "body": "#070C2A"
            },
            "backgroundImage": {
                "none": "none",
                "gradient-to-t": "linear-gradient(to top, var(--tw-gradient-stops))",
                "gradient-to-tr": "linear-gradient(to top right, var(--tw-gradient-stops))",
                "gradient-to-r": "linear-gradient(to right, var(--tw-gradient-stops))",
                "gradient-to-br": "linear-gradient(to bottom right, var(--tw-gradient-stops))",
                "gradient-to-b": "linear-gradient(to bottom, var(--tw-gradient-stops))",
                "gradient-to-bl": "linear-gradient(to bottom left, var(--tw-gradient-stops))",
                "gradient-to-l": "linear-gradient(to left, var(--tw-gradient-stops))",
                "gradient-to-tl": "linear-gradient(to top left, var(--tw-gradient-stops))",
                "radial": "radial-gradient(var(--tw-gradient-stops))",
                "gradient-violet-blue": "linear-gradient(90deg, #826FFF 0%, #511CFF 48.96%, #00D1FF 100%)",
                "gradient-tag": "linear-gradient(158deg, #826FFF 1.14%, #511CFF 51.87%, #00D1FF 104.76%)"
            },
            "backgroundPosition": {
                "bottom": "bottom",
                "center": "center",
                "left": "left",
                "left-bottom": "left bottom",
                "left-top": "left top",
                "right": "right",
                "right-bottom": "right bottom",
                "right-top": "right top",
                "top": "top"
            },
            "backgroundSize": {
                "auto": "auto",
                "cover": "cover",
                "contain": "contain"
            },
            "blur": {
                "0": "0",
                "none": "0",
                "sm": "4px",
                "DEFAULT": "8px",
                "md": "12px",
                "lg": "16px",
                "xl": "24px",
                "2xl": "40px",
                "3xl": "64px"
            },
            "brightness": {
                "0": "0",
                "50": ".5",
                "75": ".75",
                "90": ".9",
                "95": ".95",
                "100": "1",
                "105": "1.05",
                "110": "1.1",
                "125": "1.25",
                "150": "1.5",
                "200": "2"
            },
            "borderColor": {
                "DEFAULT": "#DFDFDF"
            },
            "borderRadius": {
                "none": "0",
                "xs": "0.125rem",
                "sm": "0.25rem",
                "DEFAULT": "0.25rem",
                "md": "0.375rem",
                "lg": "0.5rem",
                "xl": "0.625rem",
                "2xl": "1rem",
                "3xl": "1.25rem",
                "4xl": "1.875rem",
                "5xl": "2.5rem",
                "full": "9999px"
            },
            "borderWidth": {
                "0": "0",
                "2": "2px",
                "3": "3px",
                "4": "4px",
                "8": "8px",
                "DEFAULT": "1px"
            },
            "boxShadow": {
                "sm": "0px 1px 2px 0px rgba(156, 163, 175, 0.30)",
                "DEFAULT": "0px 6px 10px -2px rgba(85, 105, 135, 0.04), 0px 2px 4px -2px rgba(85, 105, 135, 0.02)",
                "md": "0px 12px 16px -4px rgba(85, 105, 135, 0.04), 0px 4px 6px -2px rgba(85, 105, 135, 0.02)",
                "lg": "0px 20px 24px -4px rgba(85, 105, 135, 0.04), 0px 8px 8px -4px rgba(85, 105, 135, 0.02)",
                "xl": "0px 2px 4px -2px rgba(42, 51, 66, 0.03), 0px 6px 10px -2px rgba(42, 51, 66, 0.06)",
                "2xl": "0px 32px 64px -12px rgba(85, 105, 135, 0.08)",
                "3xl": "0px 0px 8px 0px rgba(255, 255, 255, 0.20)",
                "4xl": "0px 0px 8px 0px rgba(209, 213, 219, 0.50)",
                "inner": "inset 0 2px 4px 0 rgb(0 0 0 / 0.05)",
                "none": "none"
            },
            "contrast": {
                "0": "0",
                "50": ".5",
                "75": ".75",
                "100": "1",
                "125": "1.25",
                "150": "1.5",
                "200": "2"
            },
            "cursor": {
                "auto": "auto",
                "DEFAULT": "default",
                "pointer": "pointer",
                "wait": "wait",
                "text": "text",
                "move": "move",
                "not-allowed": "not-allowed"
            },
            "dropShadow": {
                "sm": "0 1px 1px rgba(0,0,0,0.05)",
                "DEFAULT": [
                    "0 1px 2px rgba(0, 0, 0, 0.1)",
                    "0 1px 1px rgba(0, 0, 0, 0.06)"
                ],
                "md": [
                    "0 4px 3px rgba(0, 0, 0, 0.07)",
                    "0 2px 2px rgba(0, 0, 0, 0.06)"
                ],
                "lg": [
                    "0 10px 8px rgba(0, 0, 0, 0.04)",
                    "0 4px 3px rgba(0, 0, 0, 0.1)"
                ],
                "xl": [
                    "0 20px 13px rgba(0, 0, 0, 0.03)",
                    "0 8px 5px rgba(0, 0, 0, 0.08)"
                ],
                "2xl": "0 25px 25px rgba(0, 0, 0, 0.15)",
                "none": "0 0 #0000"
            },
            "fill": {
                "current": "currentColor"
            },
            "grayscale": {
                "0": "0",
                "DEFAULT": "100%"
            },
            "hueRotate": {
                "0": "0deg",
                "15": "15deg",
                "30": "30deg",
                "60": "60deg",
                "90": "90deg",
                "180": "180deg",
                "-180": "-180deg",
                "-90": "-90deg",
                "-60": "-60deg",
                "-30": "-30deg",
                "-15": "-15deg"
            },
            "invert": {
                "0": "0",
                "DEFAULT": "100%"
            },
            "flex": {
                "1": "1 1 0%",
                "auto": "1 1 auto",
                "initial": "0 1 auto",
                "none": "none"
            },
            "flexGrow": {
                "0": "0",
                "DEFAULT": "1"
            },
            "flexShrink": {
                "0": "0",
                "DEFAULT": "1"
            },
            "fontFamily": {
                "body": "\"Inter\", ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, \"Segoe UI\", Roboto, \"Helvetica Neue\", Arial, \"Noto Sans\", sans-serif, \"Apple Color Emoji\", \"Segoe UI Emoji\", \"Segoe UI Symbol\", \"Noto Color Emoji\"",
                "heading": "\"Poppins\", ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, \"Segoe UI\", Roboto, \"Helvetica Neue\", Arial, \"Noto Sans\", sans-serif, \"Apple Color Emoji\", \"Segoe UI Emoji\", \"Segoe UI Symbol\", \"Noto Color Emoji\"",
                "sans": "Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, \"Segoe UI\", Roboto, \"Helvetica Neue\", Arial, \"Noto Sans\", sans-serif, \"Apple Color Emoji\", \"Segoe UI Emoji\", \"Segoe UI Symbol\", \"Noto Color Emoji\"",
                "serif": "ui-serif, Georgia, Cambria, \"Times New Roman\", Times, serif",
                "mono": "ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, \"Liberation Mono\", \"Courier New\", monospace"
            },
            "fontSize": {
                "xs": [
                    "0.75rem",
                    {
                        "lineHeight": "1.5"
                    }
                ],
                "2xs": [
                    "0.69rem",
                    {
                        "lineHeight": "1.5"
                    }
                ],
                "sm": [
                    "0.875rem",
                    {
                        "lineHeight": "1.5"
                    }
                ],
                "base": [
                    "1rem",
                    {
                        "lineHeight": "1.5"
                    }
                ],
                "lg": [
                    "1.125rem",
                    {
                        "lineHeight": "1.5"
                    }
                ],
                "xl": [
                    "1.25rem",
                    {
                        "lineHeight": "1.4"
                    }
                ],
                "2xl": [
                    "1.5rem",
                    {
                        "lineHeight": "1.3"
                    }
                ],
                "3xl": [
                    "1.875rem",
                    {
                        "lineHeight": "1.3"
                    }
                ],
                "4xl": [
                    "2.25rem",
                    {
                        "lineHeight": "1.2"
                    }
                ],
                "5xl": [
                    "3rem",
                    {
                        "lineHeight": "1.25"
                    }
                ],
                "6xl": [
                    "3.75rem",
                    {
                        "lineHeight": "1"
                    }
                ],
                "7xl": [
                    "4.5rem",
                    {
                        "lineHeight": "1.25"
                    }
                ],
                "8xl": [
                    "6.25rem",
                    {
                        "lineHeight": "1.25"
                    }
                ],
                "9xl": [
                    "7.5rem",
                    {
                        "lineHeight": "1.25"
                    }
                ],
                "10xl": [
                    "8.75rem",
                    {
                        "lineHeight": "1.25"
                    }
                ]
            },
            "fontWeight": {
                "hairline": "100",
                "thin": "200",
                "light": "300",
                "normal": "400",
                "medium": "500",
                "semibold": "600",
                "bold": "700",
                "extrabold": "800",
                "black": "900"
            },
            "gridAutoColumns": {
                "auto": "auto",
                "min": "min-content",
                "max": "max-content",
                "fr": "minmax(0, 1fr)"
            },
            "gridAutoRows": {
                "auto": "auto",
                "min": "min-content",
                "max": "max-content",
                "fr": "minmax(0, 1fr)"
            },
            "gridColumn": {
                "auto": "auto",
                "span-1": "span 1 / span 1",
                "span-2": "span 2 / span 2",
                "span-3": "span 3 / span 3",
                "span-4": "span 4 / span 4",
                "span-5": "span 5 / span 5",
                "span-6": "span 6 / span 6",
                "span-7": "span 7 / span 7",
                "span-8": "span 8 / span 8",
                "span-9": "span 9 / span 9",
                "span-10": "span 10 / span 10",
                "span-11": "span 11 / span 11",
                "span-12": "span 12 / span 12",
                "span-full": "1 / -1"
            },
            "gridColumnEnd": {
                "1": "1",
                "2": "2",
                "3": "3",
                "4": "4",
                "5": "5",
                "6": "6",
                "7": "7",
                "8": "8",
                "9": "9",
                "10": "10",
                "11": "11",
                "12": "12",
                "13": "13",
                "auto": "auto"
            },
            "gridColumnStart": {
                "1": "1",
                "2": "2",
                "3": "3",
                "4": "4",
                "5": "5",
                "6": "6",
                "7": "7",
                "8": "8",
                "9": "9",
                "10": "10",
                "11": "11",
                "12": "12",
                "13": "13",
                "auto": "auto"
            },
            "gridRow": {
                "auto": "auto",
                "span-1": "span 1 / span 1",
                "span-2": "span 2 / span 2",
                "span-3": "span 3 / span 3",
                "span-4": "span 4 / span 4",
                "span-5": "span 5 / span 5",
                "span-6": "span 6 / span 6",
                "span-full": "1 / -1"
            },
            "gridRowStart": {
                "1": "1",
                "2": "2",
                "3": "3",
                "4": "4",
                "5": "5",
                "6": "6",
                "7": "7",
                "auto": "auto"
            },
            "gridRowEnd": {
                "1": "1",
                "2": "2",
                "3": "3",
                "4": "4",
                "5": "5",
                "6": "6",
                "7": "7",
                "auto": "auto"
            },
            "gridTemplateColumns": {
                "1": "repeat(1, minmax(0, 1fr))",
                "2": "repeat(2, minmax(0, 1fr))",
                "3": "repeat(3, minmax(0, 1fr))",
                "4": "repeat(4, minmax(0, 1fr))",
                "5": "repeat(5, minmax(0, 1fr))",
                "6": "repeat(6, minmax(0, 1fr))",
                "7": "repeat(7, minmax(0, 1fr))",
                "8": "repeat(8, minmax(0, 1fr))",
                "9": "repeat(9, minmax(0, 1fr))",
                "10": "repeat(10, minmax(0, 1fr))",
                "11": "repeat(11, minmax(0, 1fr))",
                "12": "repeat(12, minmax(0, 1fr))",
                "none": "none"
            },
            "gridTemplateRows": {
                "1": "repeat(1, minmax(0, 1fr))",
                "2": "repeat(2, minmax(0, 1fr))",
                "3": "repeat(3, minmax(0, 1fr))",
                "4": "repeat(4, minmax(0, 1fr))",
                "5": "repeat(5, minmax(0, 1fr))",
                "6": "repeat(6, minmax(0, 1fr))",
                "none": "none"
            },
            "height": {
                "auto": "auto",
                "full": "100%",
                "screen": "100vh"
            },
            "inset": {
                "auto": "auto",
                "full": "100%"
            },
            "keyframes": {
                "spin": {
                    "to": {
                        "transform": "rotate(360deg)"
                    }
                },
                "ping": {
                    "75%, 100%": {
                        "transform": "scale(2)",
                        "opacity": "0"
                    }
                },
                "pulse": {
                    "50%": {
                        "opacity": ".5"
                    }
                },
                "bounce": {
                    "0%, 100%": {
                        "transform": "translateY(-25%)",
                        "animationTimingFunction": "cubic-bezier(0.8,0,1,1)"
                    },
                    "50%": {
                        "transform": "none",
                        "animationTimingFunction": "cubic-bezier(0,0,0.2,1)"
                    }
                }
            },
            "letterSpacing": {
                "10xl": "-4.8px",
                "9xl": "-4px",
                "8xl": "-3.6px",
                "7xl": "-1.86px",
                "6xl": "-1.44px",
                "5xl": "-0.96px",
                "4xl": "-2px",
                "3xl": "-0.64px",
                "2xl": "-0.48px",
                "tightest": "-0.05em",
                "tighter": "-0.04em",
                "tight": "-0.02em",
                "normal": "0em",
                "wide": "0.025em",
                "wider": "0.05em",
                "widest": "0.1em"
            },
            "lineHeight": {
                "3": ".75rem",
                "4": "1rem",
                "5": "1.25rem",
                "6": "1.5rem",
                "7": "1.75rem",
                "8": "2rem",
                "9": "2.25rem",
                "10": "2.5rem",
                "none": "1",
                "tight": "1.25",
                "snug": "1.375",
                "normal": "1.5",
                "relaxed": "1.625",
                "loose": "2"
            },
            "listStyleType": {
                "none": "none",
                "disc": "disc",
                "decimal": "decimal"
            },
            "margin": {
                "auto": "auto"
            },
            "maxHeight": {
                "full": "100%",
                "screen": "100vh"
            },
            "maxWidth": {
                "0": "0rem",
                "none": "none",
                "xs": "20rem",
                "sm": "24rem",
                "md": "28rem",
                "lg": "32rem",
                "xl": "36rem",
                "2xl": "42rem",
                "3xl": "48rem",
                "4xl": "50rem",
                "5xl": "56rem",
                "6xl": "63rem",
                "7xl": "72rem",
                "8xl": "76rem",
                "full": "100%",
                "min": "min-content",
                "max": "max-content",
                "prose": "65ch"
            },
            "minHeight": {
                "0": "0",
                "full": "100%",
                "screen": "100vh"
            },
            "minWidth": {
                "0": "0",
                "full": "100%",
                "min": "min-content",
                "max": "max-content"
            },
            "objectPosition": {
                "bottom": "bottom",
                "center": "center",
                "left": "left",
                "left-bottom": "left bottom",
                "left-top": "left top",
                "right": "right",
                "right-bottom": "right bottom",
                "right-top": "right top",
                "top": "top"
            },
            "opacity": {
                "0": "0",
                "5": "0.05",
                "10": "0.1",
                "20": "0.2",
                "25": "0.25",
                "30": "0.3",
                "40": "0.4",
                "50": "0.5",
                "60": "0.6",
                "70": "0.7",
                "75": "0.75",
                "80": "0.8",
                "90": "0.9",
                "95": "0.95",
                "100": "1"
            },
            "order": {
                "1": "1",
                "2": "2",
                "3": "3",
                "4": "4",
                "5": "5",
                "6": "6",
                "7": "7",
                "8": "8",
                "9": "9",
                "10": "10",
                "11": "11",
                "12": "12",
                "first": "-9999",
                "last": "9999",
                "none": "0"
            },
            "outline": {
                "none": [
                    "2px solid transparent",
                    "2px"
                ],
                "white": [
                    "2px dotted white",
                    "2px"
                ],
                "black": [
                    "2px dotted black",
                    "2px"
                ]
            },
            "ringColor": {
                "DEFAULT": "theme"
            },
            "ringOffsetWidth": {
                "0": "0px",
                "1": "1px",
                "2": "2px",
                "4": "4px",
                "8": "8px"
            },
            "ringOpacity": {
                "DEFAULT": "0.5"
            },
            "ringWidth": {
                "0": "0px",
                "1": "1px",
                "2": "2px",
                "4": "4px",
                "8": "8px",
                "DEFAULT": "3px"
            },
            "rotate": {
                "0": "0deg",
                "1": "1deg",
                "2": "2deg",
                "3": "3deg",
                "6": "6deg",
                "12": "12deg",
                "45": "45deg",
                "90": "90deg",
                "180": "180deg",
                "-180": "-180deg",
                "-90": "-90deg",
                "-45": "-45deg",
                "-12": "-12deg",
                "-6": "-6deg",
                "-3": "-3deg",
                "-2": "-2deg",
                "-1": "-1deg"
            },
            "saturate": {
                "0": "0",
                "50": ".5",
                "100": "1",
                "150": "1.5",
                "200": "2"
            },
            "scale": {
                "0": "0",
                "50": ".5",
                "75": ".75",
                "90": ".9",
                "95": ".95",
                "100": "1",
                "105": "1.05",
                "110": "1.1",
                "125": "1.25",
                "150": "1.5"
            },
            "sepia": {
                "0": "0",
                "DEFAULT": "100%"
            },
            "skew": {
                "0": "0deg",
                "1": "1deg",
                "2": "2deg",
                "3": "3deg",
                "6": "6deg",
                "12": "12deg",
                "-12": "-12deg",
                "-6": "-6deg",
                "-3": "-3deg",
                "-2": "-2deg",
                "-1": "-1deg"
            },
            "stroke": {
                "current": "currentColor"
            },
            "textColor": {
                "body": "#000"
            },
            "transformOrigin": {
                "center": "center",
                "top": "top",
                "top-right": "top right",
                "right": "right",
                "bottom-right": "bottom right",
                "bottom": "bottom",
                "bottom-left": "bottom left",
                "left": "left",
                "top-left": "top left"
            },
            "transitionDelay": {
                "75": "75ms",
                "100": "100ms",
                "150": "150ms",
                "200": "200ms",
                "300": "300ms",
                "500": "500ms",
                "700": "700ms",
                "1000": "1000ms"
            },
            "transitionDuration": {
                "75": "75ms",
                "100": "100ms",
                "150": "150ms",
                "200": "200ms",
                "300": "300ms",
                "500": "500ms",
                "700": "700ms",
                "1000": "1000ms",
                "DEFAULT": "150ms"
            },
            "transitionProperty": {
                "none": "none",
                "all": "all",
                "DEFAULT": "background-color, border-color, color, fill, stroke, opacity, box-shadow, transform, filter, backdrop-filter",
                "colors": "background-color, border-color, color, fill, stroke",
                "opacity": "opacity",
                "shadow": "box-shadow",
                "transform": "transform"
            },
            "transitionTimingFunction": {
                "DEFAULT": "cubic-bezier(0.4, 0, 0.2, 1)",
                "linear": "linear",
                "in": "cubic-bezier(0.4, 0, 1, 1)",
                "out": "cubic-bezier(0, 0, 0.2, 1)",
                "in-out": "cubic-bezier(0.4, 0, 0.2, 1)"
            },
            "translate": {
                "full": "100%"
            },
            "width": {
                "auto": "auto",
                "full": "100%",
                "screen": "100vw"
            },
            "zIndex": {
                "0": "0",
                "10": "10",
                "20": "20",
                "30": "30",
                "40": "40",
                "50": "50",
                "auto": "auto"
            },
        },
    },

    plugins: [forms, typography],
};
