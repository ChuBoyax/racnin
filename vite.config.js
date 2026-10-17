import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { bunny } from 'laravel-vite-plugin/fonts';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
            fonts: [
                bunny('Instrument Sans', {
                    weights: [400, 500, 600, 700],
                    preload: [{ weight: 400 }, { weight: 700 }],
                }),
                bunny('Bricolage Grotesque', {
                    weights: [700, 800],
                    preload: [{ weight: 800 }],
                }),
                bunny('Instrument Serif', {
                    weights: [400],
                    styles: ['normal', 'italic'],
                    preload: [{ weight: 400, style: 'italic' }],
                }),
            ],
        }),
        tailwindcss(),
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
