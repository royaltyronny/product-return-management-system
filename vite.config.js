import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import fs from 'fs';

// Helper function to dynamically load input files from specific directories
function getInputFiles(directory) {
    return fs.readdirSync(directory).map(file => `${directory}/${file}`);
}

// Check the current environment
const isProduction = process.env.NODE_ENV === 'production';

export default defineConfig({
    plugins: [
        // Laravel Vite Plugin to handle Laravel-specific asset management
        laravel({
            input: [
                'resources/sass/app.scss',
                'resources/js/app.js',
                // Dynamically add other input files if needed
                ...getInputFiles('resources/js/components')
            ],
            refresh: true, // Enable hot module reloading during development
        }),
        // Vue plugin with asset URL transformation settings
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
    resolve: {
        alias: {
            // Ensure the correct Vue build is used
            vue: 'vue/dist/vue.esm-bundler.js',
        },
    },
    build: {
        // Enable code splitting for optimization
        rollupOptions: {
            output: {
                manualChunks: {
                    vendor: ['vue'], // Split vendor files into a separate chunk
                },
            },
        },
        // Minify during production builds
        minify: isProduction,
    },
    server: {
        // Configure server settings for development
        host: 'localhost',
        port: 3000,
        strictPort: true, // Ensure the port is strictly used
    },
});
