import defaultTheme from "tailwindcss/defaultTheme";
import forms from "@tailwindcss/forms";
import containerQueries from "@tailwindcss/container-queries";

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: "class",

    content: [
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
        "./storage/framework/views/*.php",
        "./resources/views/**/*.blade.php",
    ],

    theme: {
        extend: {
            colors: {
                primary: "var(--color-primary, #0d59f2)",
                "background-light": "#f5f6f8",
                "background-dark": "#101622",
                "surface-dark": "#1e2430",
                "border-dark": "#2d3646",
            },
            fontFamily: {
                sans: ["Figtree", ...defaultTheme.fontFamily.sans],
                display: ["Manrope", "sans-serif"],
            },
        },
    },

    plugins: [forms, containerQueries],
};
