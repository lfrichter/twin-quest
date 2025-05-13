//import { defineConfig } from 'vite';
import { defineConfig } from 'vitest/config'
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
  plugins: [
    laravel(['resources/js/app.ts']),
    vue({ script: { defineModel: true, propsDestructure: true } }),
  ],
  resolve: { alias: { '@': '/resources/js' } },
  test: {
    environment: 'jsdom',
    // "setupFiles" caso queira rodar scripts antes dos testes
    // setupFiles: ['./src/tests/setup.ts'],
    globals: true, // Se quiser usar describe, it, expect sem importar
  },
});
