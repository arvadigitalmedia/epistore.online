import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                // Primary: Biru Cerah (#3498db)
                primary: {
                    50: '#f0f7fd',
                    100: '#e1effb',
                    200: '#c3def7',
                    300: '#95c6f1',
                    400: '#5ba3e9',
                    500: '#3498db', // Base
                    600: '#217dbb',
                    700: '#1a6496',
                    800: '#18547e',
                    900: '#184668',
                },
                // Secondary: Emas/Kuning (#f1c40f)
                secondary: {
                    50: '#fefce8',
                    100: '#fff9c2',
                    200: '#fff087',
                    300: '#ffe244',
                    400: '#f1c40f', // Base (adjusted to match palette flow, user asked for f1c40f as main)
                    500: '#f1c40f', // Using user's color as 500
                    600: '#d9a406',
                    700: '#ad7906',
                    800: '#8e600b',
                    900: '#784f0f',
                },
            },
        },
    },

    plugins: [forms],
};
