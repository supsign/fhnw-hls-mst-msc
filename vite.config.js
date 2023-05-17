import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import path from 'path';
import vue from '@vitejs/plugin-vue';

export default defineConfig(({ command, mode }) => {
  return {
    plugins: [
      laravel({
        input: ['resources/js/app.ts'],
        refresh: true
      }),
      vue({
        template: {
          transformAssetUrls: {
            base: null,
            includeAbsolute: false
          }
        }
      })
    ],
    resolve: {
      alias: {
        '@': path.resolve(__dirname, './resources/js')
      }
    },
    build: {
      rollupOptions: {
        output: {
          assetFileNames: mode === 'development' ? 'css/[name].[ext]' : 'css/[name].[hash].[ext]',
          entryFileNames: mode === 'development' ? 'js/[name].js' : 'js/[name].[hash].js'
        }
      },
      reportCompressedSize: mode === 'development' ? false : true
    }
  };
});
