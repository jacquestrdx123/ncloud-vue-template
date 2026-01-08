import './bootstrap';
import '../css/app.css';
import { createApp, h } from 'vue/dist/vue.esm-bundler.js';
import { createInertiaApp, Link, router } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createPinia } from 'pinia';
import { ZiggyVue } from 'ziggy';
import { useAppStore } from './stores/appStore';

const pinia = createPinia();

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    runtimeCompiler: true,
    transpileDependencies: true,
    setup({ el, App, props, plugin }) {
        const vueApp = createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(pinia)
            .use(ZiggyVue)
            .component('inertia-link', Link);
        
        // Initialize app store with Inertia props
        const appStore = useAppStore();
        const inertiaProps = props.initialPage?.props || props;
        
        // Initialize store BEFORE mounting to apply theme early
        appStore.initialize(inertiaProps);
        
        // Mount app
        const mountedApp = vueApp.mount(el);
        
        // Reapply theme on Inertia visits to ensure it stays correct
        // This ensures theme is applied even if page navigation happens
        router.on('success', () => {
            appStore.applyTheme();
        });
        
        return mountedApp;
    },
    progress: {
        color: '#4B5563',
    },
});
