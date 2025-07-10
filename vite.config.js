import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import path from 'path';

export default defineConfig(({ command, mode }) => {
  const isProduction = mode === 'production';

  return {
    plugins: [
      laravel({
        input: ['resources/css/app.css', 'resources/js/app.js'],
        refresh: true,
        // Add this:
        base: isProduction ? 'https://sheet-simulation-1.onrender.com/build/' : '/',
      }),
      vue(),
    ],
    resolve: {
      alias: {
        '@': path.resolve(__dirname, 'resources/js'),
      },
    },
    server: {
      host: '127.0.0.1',
      port: 5173,
    },
  };
});
