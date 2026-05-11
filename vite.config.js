import {defineConfig} from "vite";
import tailwindcss from '@tailwindcss/vite';
import vue from '@vitejs/plugin-vue';
import path from 'path';
import laravel from "laravel-vite-plugin";

export default defineConfig({
    server: {
        cors: true,
        hmr: {
            host: 'localhost',
        },
        watch: {
            ignored: [
                '**/vendor/**',
                '**/node_modules/**',
                '**/runtime/**',
                '**/.git/**',
            ]
        },
    },
    plugins: [
        laravel({
            input: ['resources/css/main.css', 'resources/js/main.js'],
            refresh: true,
        }), tailwindcss(), vue(),
    ],
    resolve: {
        alias: {
            '@': path.resolve(__dirname, './resources'),
            '@js': path.resolve(__dirname, './resources/js'),
        },
    },
})
