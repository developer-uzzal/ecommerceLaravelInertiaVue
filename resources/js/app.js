import { createApp, h } from 'vue'
import { createInertiaApp } from '@inertiajs/vue3'

import "bootstrap/dist/css/bootstrap.min.css";
import "bootstrap";

import Notify from 'simple-notify'
import 'simple-notify/dist/simple-notify.css'

import Vue3EasyDataTable from 'vue3-easy-data-table';
import 'vue3-easy-data-table/dist/style.css';

createInertiaApp({
  resolve: name => {
    const pages = import.meta.glob('./Pages/**/*.vue', { eager: true })
    return pages[`./Pages/${name}.vue`]
  },
  setup({ el, App, props, plugin }) {
    createApp({ render: () => h(App, props) })
      .use(plugin)
      .component('EasyDataTable', Vue3EasyDataTable)
      .mount(el)
  },
  
  progress: {
    // The delay after which the progress bar will appear, in milliseconds...
    delay: 250,

    // The color of the progress bar...
    color: '#29d',

    // Whether to include the default NProgress styles...
    includeCSS: true,

    // Whether the NProgress spinner will be shown...
    showSpinner: false,
  },
})