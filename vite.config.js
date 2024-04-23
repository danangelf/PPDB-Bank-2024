import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js', 'resources/css/tom-select.default.css', 'resources/css/font-awesome.css'],
            refresh: true,
        }),
    ],
});
