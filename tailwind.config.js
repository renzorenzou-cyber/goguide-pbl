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
    colors: {
        brand: {
            blue: '#1e5288',
            orange: '#f26522',
            darkBlue: '#133961',
        },
    },
    fontFamily: {
        sans: ['Plus Jakarta Sans', ...defaultTheme.fontFamily.sans],
    },
},
    },

    plugins: [forms],
};
