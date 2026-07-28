import { defineConfig } from 'vite'
import path from 'path'
import tailwindcss from '@tailwindcss/vite'
import react from '@vitejs/plugin-react'


function figmaAssetResolver() {
  return {
    name: 'figma-asset-resolver',
    resolveId(id) {
      if (id.startsWith('figma:asset/')) {
        const filename = id.replace('figma:asset/', '')
        return path.resolve(__dirname, 'src/assets', filename)
      }
    },
  }
}

export default defineConfig({
  plugins: [
    figmaAssetResolver(),
    // The React and Tailwind plugins are both required for Make, even if
    // Tailwind is not being actively used – do not remove them
    react(),
    tailwindcss(),
  ],
  resolve: {
    alias: {
      // Alias @ to the src directory
      '@': path.resolve(__dirname, './src'),
    },
  },

  // File types to support raw imports. Never add .css, .tsx, or .ts files to this.
  assetsInclude: ['**/*.svg', '**/*.csv'],

  build: {
    // Alvo moderno = bundle menor (sem transpile desnecessário) e melhor desempenho.
    target: 'es2020',
    // CSS por rota some com o custo de baixar todo o CSS no primeiro paint.
    cssCodeSplit: true,
    // Sourcemaps fora do bundle de produção (menos bytes servidos).
    sourcemap: false,
    // Inline só de assets muito pequenos; o resto vira arquivo cacheável.
    assetsInlineLimit: 2048,
    // Divide dependências grandes em chunks próprios: melhora o cache entre
    // deploys e paraleliza o download. framer-motion e react ficam separados
    // do código do app, que muda com mais frequência.
    rollupOptions: {
      output: {
        manualChunks: {
          'vendor-motion': ['motion', 'framer-motion'],
          'vendor-react': ['react', 'react-dom', 'react-router'],
        },
      },
    },
  },
})
