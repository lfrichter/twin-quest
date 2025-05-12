import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
  plugins: [
    laravel(['resources/js/app.ts']),
    vue({ script: { defineModel: true, propsDestructure: true } }),
  ],
  resolve: { alias: { '@': '/resources/js' } },
});
