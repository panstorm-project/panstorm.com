import laravel from 'laravel-vite-plugin';
import { defineConfig } from 'vite';

export default defineConfig({
    build: {
        outDir: 'public',
        rollupOptions: {
            input: 'resources/js/monitor/app.ts',
            output: {
                entryFileNames: 'monitor.js',
                format: 'iife',
            },
        },
        manifest: false, // Disable the manifest file
        assetsDir: '', // Disable the assets folder
        emptyOutDir: false, // Prevent Vite from removing the contents of outDir
    },
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.ts'],
            refresh: true,
        }),
    ],
});
