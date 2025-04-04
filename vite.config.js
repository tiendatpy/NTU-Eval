import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import path from 'path';
import sassGlobImporter from 'sass-glob-importer';

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
        preprocessorOptions: {
            scss: {
                importer: sassGlobImporter(),
            },
        },
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
