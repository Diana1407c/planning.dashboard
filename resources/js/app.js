import "./bootstrap.js";

import { createApp, h } from 'vue';
import { App as InertiaApp, plugin as InertiaPlugin } from '@inertiajs/inertia-vue3';
import { InertiaProgress } from '@inertiajs/progress';
import '../css/app.scss';
import 'vue-final-modal/style.css'
import "vue-select/dist/vue-select.css";
import '@vuepic/vue-datepicker/dist/main.css';

const adminApp = createApp({
    render: () =>
        h(InertiaApp, {
            initialPage: JSON.parse(document.getElementById('backpack').dataset.page),
            resolveComponent: (name) => import(/* webpackChunkName: "[request]" */`./components/CustomPages/${name}.vue`).then((module) => module.default),
        }),
});

adminApp.use(InertiaPlugin);

adminApp.mount('#backpack')

InertiaProgress.init({ color: '#4B5563' });

