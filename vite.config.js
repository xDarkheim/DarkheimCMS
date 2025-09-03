import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import { resolve } from 'path';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.scss',
                'resources/css/admin.scss',
                'resources/js/app.js',
                'resources/js/admin.js'
            ],
            refresh: true,
        }),
        vue(),
    ],
    resolve: {
        alias: {
            '@': resolve(__dirname, 'resources'),
        },
    },
    build: {
        rollupOptions: {
            output: {
                assetFileNames: (assetInfo) => {
                    if (assetInfo.name && assetInfo.name.endsWith('.woff2')) {
                        return 'fonts/[name][extname]';
                    }
                    return 'assets/[name]-[hash][extname]';
                }
            }
        }
    },
    assetsInclude: ['**/*.woff2', '**/*.woff', '**/*.ttf'],
    publicDir: false, // Disable Vite's public directory feature for Laravel
});
