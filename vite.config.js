import { fileURLToPath, URL } from 'node:url';
import tailwindcss from '@tailwindcss/vite';
import vue from '@vitejs/plugin-vue';
import laravel from 'laravel-vite-plugin';
import AutoImport from 'unplugin-auto-import/vite';
import Components from 'unplugin-vue-components/vite';
import { defineConfig } from 'vite';

export default defineConfig(({ mode }) => {
  const isDevBuild = mode === 'development';

  return {
    build: {
      reportCompressedSize: !isDevBuild,
      rollupOptions: {
        output: {
          assetFileNames: isDevBuild ? 'css/[name].[ext]' : 'css/[name].[hash].[ext]',
          entryFileNames: isDevBuild ? 'js/[name].js' : 'js/[name].[hash].js',
        },
      },
    },
    plugins: [
      tailwindcss(),
      AutoImport({
        defaultExportByFilename: true,
        dts: 'resources/js/types/auto-imports.d.ts',
        imports: ['vue'],
        vueTemplate: true,
      }),
      Components({ dirs: ['resources/js/components'], dts: 'resources/js/components.d.ts' }),
      laravel({ input: ['resources/js/app.ts'], refresh: true }),
      vue({ template: { transformAssetUrls: { base: null, includeAbsolute: false } } }),
    ],
    resolve: { alias: { '@': fileURLToPath(new URL('resources/js', import.meta.url)) } },
  };
});
