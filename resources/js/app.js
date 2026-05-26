import '../css/app.css';
import '@fortawesome/fontawesome-free/css/all.min.css';
import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';

const pages = import.meta.glob('./pages/**/*.vue', { eager: true });

createInertiaApp({
    title: (title) => `${title} | Cloth Rental Management System`,
    resolve: (name) => {
        const page = pages[`./pages/${name}.vue`];

        if (! page) {
            throw new Error(`Unknown Inertia page: ${name}`);
        }

        return page;
    },
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .mount(el);
    },
});
