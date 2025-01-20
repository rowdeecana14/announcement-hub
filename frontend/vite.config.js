// FILE: vite.config.js

import { fileURLToPath } from 'node:url'
import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import { quasar, transformAssetUrls } from '@quasar/vite-plugin'

// https://vitejs.dev/config/
export default defineConfig({
  resolve: {
    alias: [
      {
        find: "@app",
        replacement: fileURLToPath(new URL('./', import.meta.url)),
      },
      {
        find: "@pages",
        replacement: fileURLToPath(new URL('./src/pages', import.meta.url)),
      },
      {
        find: "@components",
        replacement: fileURLToPath(new URL('./src/components', import.meta.url)),
      },
      {
        find: "@layouts",
        replacement: fileURLToPath(new URL('./src/layouts', import.meta.url)),
      },
      {
        find: "@utils",
        replacement: fileURLToPath(new URL('./src/utils', import.meta.url)),
      },
      {
        find: "@store",
        replacement: fileURLToPath(new URL('./src/store', import.meta.url)),
      },
      {
        find: "@validations",
        replacement: fileURLToPath(new URL('./src/validations', import.meta.url)),
      },
    ],
  },
  
  plugins: [
    vue({
      template: { transformAssetUrls }
    }),
    

    // @quasar/plugin-vite options list:
    // https://github.com/quasarframework/quasar/blob/dev/vite-plugin/index.d.ts
    quasar({
      sassVariables: fileURLToPath(
        new URL('./src/quasar-variables.sass', import.meta.url)
      )
    })
  ]
})
