import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import { resolve } from 'path';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.scss', 'resources/js/app.js'],
            refresh: true,
        }),
        vue(),
    ],
    resolve: {
        alias: {
            '@': resolve(__dirname, 'resources'),
        },
    },
    server: {
        proxy: {
            '/api': {
                target: 'http://localhost:8000',
                changeOrigin: true,
                secure: false,
            },
        },
    },
    build: {
        rollupOptions: {
            external: [],
        },
        assetsDir: 'assets',
        copyPublicDir: true,
    },
    publicDir: 'public',
    assetsInclude: ['**/*.woff', '**/*.woff2', '**/*.ttf', '**/*.eot', '**/*.svg'],
    css: {
        preprocessorOptions: {
            scss: {
                additionalData: `
                    $fa-font-path: '/fonts' !default;
                `
            }
        }
    }
});
