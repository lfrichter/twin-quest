import './bootstrap';
import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createPinia } from 'pinia';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
  title: (title) => `${title} - ${appName}`,
  resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
  // resolve: (name: string): DefineComponent | { default: DefineComponent } => {
  //   const pages = import.meta.glob('./Pages/**/*.vue', { eager: true });
  //   const page = pages[`./Pages/${name}.vue`];
  //   if (!page) {
  //     throw new Error(`Component not found: ./Pages/${name}.vue`);
  //   }
  //   return page as DefineComponent | { default: DefineComponent };
  // },
  setup({ el, App, props, plugin }) {
    return createApp({ render: () => h(App, props) })
      .use(plugin)
      .use(createPinia())
      .use(ZiggyVue)
      .mount(el);
  },
});
