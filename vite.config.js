import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue';
import laravel from 'laravel-vite-plugin';
import ViteNotifier from 'vite-plugin-notifier';

export default defineConfig({
  base:'/public/build/',
  resolve: {
    alias: {
      '@': '/resources/js',
      'vue': 'vue/dist/vue.esm-bundler.js',
    },
  },
  plugins: [
    laravel({
      input: [
        'resources/js/app.js',
        'resources/js/web.js'
      ],
      refresh: true,
    }),
    vue(),
    ViteNotifier({
      title: 'Vite Build 🔔',
      alwaysNotify: true,
      contentImage: null,
      sound: false,
    }),
  ],
});





















//
// import { defineConfig } from 'vite';
// import vue from '@vitejs/plugin-vue';
// import laravel from 'laravel-vite-plugin';
// import ViteNotifier from 'vite-plugin-notifier';
//
// export default defineConfig({
//   server: {
//     host: '192.168.11.247', // 👈 IMPORTANT
//     port: 5173,
//     strictPort: true,
//     cors: true, // 👈 CORS allow
//     hmr: {
//       host: '192.168.11.247', // 👈 same IP
//     },
//   },
//
//   resolve: {
//     alias: {
//       '@': '/resources/js',
//       vue: 'vue/dist/vue.esm-bundler.js',
//     },
//   },
//
//   plugins: [
//     laravel({
//       input: [
//         'resources/js/app.js',
//         'resources/js/web.js',
//       ],
//       refresh: true,
//     }),
//
//     vue(),
//
//     ViteNotifier({
//       title: 'Vite Build 🔔',
//       alwaysNotify: true,
//       contentImage: null,
//       sound: false,
//     }),
//   ],
// });