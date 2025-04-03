import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import path from 'path';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/styles/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    resolve: {
        alias: {
            '@': path.resolve(__dirname, 'resources'),
        },
    },
    css: {
        postcss: {
            plugins: [
                require('tailwindcss')({
                    config: './resources/styles/config/tailwind.config.js',
                }),
                require('autoprefixer'),
            ],
        },
    },
});
