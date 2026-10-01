import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/styles/app.scss',
                'resources/js/app.js',
                // Tailwind
                'resources/css/app.css',
                // * Custom CSS
                'resources/scss/main.scss',
                // * Custom JS
                'resources/js/main.js',
                'resources/js/master/main.js',
                'resources/js/admin/main.js',
                'resources/js/app.js'
            ],
            refresh: true,
        }),
    ],
    resolve: {
        alias: {
            '@': '/resources',
            'assets': '/public/assets',
        }
    },
    build: {
        rollupOptions: {
            output: {
                entryFileNames: 'assets/js/[name]-[hash].js',
                chunkFileNames: 'assets/js/[name]-[hash].js',
                assetFileNames: 'assets/[ext]/[name]-[hash][extname]',
            },
        },
    },
    // No funciona bien en esta versión, importar manualmente
    // css: {
    //     preprocessorOptions: {
    //         scss: {
    //             additionalData: `
    //                 @import "./resources/scss/variables.scss";
    //             `, // Si tienes variables SCSS globales
    //         },
    //     },
    // },
});
