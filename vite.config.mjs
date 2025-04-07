// vite.config.mjs

import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import path from 'path';
import tailwindcss from 'tailwindcss';
import autoprefixer from 'autoprefixer';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/styles/app.scss', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    resolve: {
        alias: {
            '@': path.resolve(__dirname, 'resources'),
            'jquery': path.resolve(__dirname, 'node_modules/jquery/dist/jquery.js'),
        },
    },
    build: {
        commonjsOptions: {
            include: ['node_modules/jquery/**'],
        },
    },
    css: {
        postcss: {
            plugins: [
                tailwindcss({
                    config: './resources/styles/config/tailwind.config.js',
                }),
                autoprefixer,
            ],
        },
    },
});
