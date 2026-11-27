import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        tailwindcss(),
    ],
    server: {
        // Default to localhost for desktop dev. Set VITE_HOST to your PC's
        // LAN IP (e.g. 192.168.1.20) to test on a phone over Wi-Fi.
        host: process.env.VITE_HOST || 'localhost',
        port: 5173,
        strictPort: true,
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});