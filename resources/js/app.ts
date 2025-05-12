import './bootstrap';
import { createApp, h, DefineComponent } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { createPinia } from 'pinia';

createInertiaApp({
  resolve: (name: string): DefineComponent | { default: DefineComponent } => {
    const pages = import.meta.glob('./Pages/**/*.vue', { eager: true });
    const page = pages[`./Pages/${name}.vue`];
    if (!page) {
      throw new Error(`Component not found: ./Pages/${name}.vue`);
    }
    return page as DefineComponent | { default: DefineComponent };
  },
  setup({ el, App, props, plugin }) {
    createApp({ render: () => h(App, props) })
      .use(plugin)
      .use(createPinia())
      .mount(el);
  },
});
