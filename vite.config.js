import { defineConfig } from 'vite';
import { resolve } from 'path';

export default defineConfig({
	base: './',
	build: {
		outDir: 'dist',
		emptyOutDir: false,
		sourcemap: true,
		rollupOptions: {
			input: resolve(import.meta.dirname, 'src/js/app.js'),
			output: {
				entryFileNames: 'app.js',
				chunkFileNames: 'app-[name].js',
				assetFileNames: (assetInfo) => {
					if (assetInfo.name?.endsWith('.css')) return 'app.css';
					return '[name][extname]';
				},
			},
		},
	},
	css: {
		preprocessorOptions: {
			scss: {
				api: 'modern',
				loadPaths: [resolve(import.meta.dirname, 'node_modules')],
				silenceDeprecations: [
					'import',
					'if-function',
					'global-builtin',
					'color-functions',
					'abs-percent',
				],
			},
		},
	},
});
