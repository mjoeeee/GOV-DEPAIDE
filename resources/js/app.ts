import { createApp, h } from 'vue';
import { createInertiaApp, router } from '@inertiajs/vue3';
import {
    asset,
    configureDomBasePath,
    configureFetchBasePath,
    configureInertiaRouterBasePath,
    configureWayfinderBasePath,
} from './lib/basePath';

configureFetchBasePath();
configureDomBasePath();
configureInertiaRouterBasePath(router);
configureWayfinderBasePath([
    ...Object.values(import.meta.glob('./routes/**/*.ts', { eager: true })),
    ...Object.values(import.meta.glob('./actions/**/*.ts', { eager: true })),
]);

const appName = import.meta.env.VITE_APP_NAME || 'DepAIDE';

createInertiaApp({
    title: (title: string) => (title ? `${title} - ${appName}` : appName),
    resolve: (name: string) => {
        const pages = import.meta.glob('./pages/**/*.vue', { eager: true }) as Record<string, any>;
        return pages[`./pages/${name}.vue`];
    },
    setup({ el, App, props, plugin }) {
        const vueApp = createApp({ render: () => h(App, props) }).use(plugin);

        vueApp.config.globalProperties.$asset = asset;
        vueApp.provide('asset', asset);
        vueApp.mount(el);
    },
    progress: {
        color: '#007bff',
    },
});
