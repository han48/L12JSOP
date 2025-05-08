import './bootstrap';
import '../css/app.css';
import Vue3Toastify from 'vue3-toastify';
import 'vue3-toastify/dist/index.css';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import { i18nVue } from "laravel-vue-i18n";
import { trans as t } from "laravel-vue-i18n";

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';
const TranslatePlugin = {
    install(app) {
        app.config.globalProperties.trans = (key, replacements = null, prefix = "user.") => {
            key = (key + "").toLowerCase();
            key = prefix + key.replace(/ /g, "_");
            const result = t(key, replacements);
            return result === key ? '...' : result;
        };
    }
};

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .use(Vue3Toastify)
            .use(i18nVue, {
                fallbackLang: "en",
                resolve: async (lang) => {
                    const langs = import.meta.glob("../../lang/*.json");
                    return await langs[`../../lang/${lang}.json`]();
                },
            })
            .use(TranslatePlugin)
            .mount(el);
        return app;
    },
    progress: {
        color: '#4B5563',
    },
});
