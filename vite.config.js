import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    server: {
        host: '0.0.0.0',   // penting: agar bisa diakses dari IP
        port: 5173,        // default port vite
        hmr: {
            host: '192.168.1.9', // ganti dengan IP komputer kamu
        },
    },
});
