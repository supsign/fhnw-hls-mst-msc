import vue from '@vitejs/plugin-vue';
import laravel from 'laravel-vite-plugin';
import path from 'node:path';
import AutoImport from 'unplugin-auto-import/vite';
import Components from 'unplugin-vue-components/vite';
import { defineConfig } from 'vite';

export default defineConfig(({ mode }) => {
  return {
    build: {
      reportCompressedSize: mode === 'development' ? false : true,
      rollupOptions: {
        output: {
          assetFileNames: mode === 'development' ? 'css/[name].[ext]' : 'css/[name].[hash].[ext]',
          entryFileNames: mode === 'development' ? 'js/[name].js' : 'js/[name].[hash].js'
        }
      }
    },
    plugins: [
      AutoImport({
        defaultExportByFilename: true,
        dts: 'resources/js/types/auto-imports.d.ts',
        eslintrc: {
          enabled: true
        },
        imports: [
          'vue'
        ],
        vueTemplate: true
      }),
      Components({
        dirs: ['resources/js/components'],
        dts: 'resources/js/components.d.ts'
      }),
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
    }
  };
});
