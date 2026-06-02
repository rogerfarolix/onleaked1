import { createApp, h } from 'vue'
import { createInertiaApp } from '@inertiajs/vue3'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers'
import { ZiggyVue } from 'ziggy-js'

createInertiaApp({
    title: (title) => title ? `${title} — Onleaked` : 'Onleaked — Cybersecurity Intelligence',
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        const ziggy = props.initialPage.props.ziggy ?? {}

        createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue, ziggy)
            .mount(el)
    },
    progress: {
        color: '#8b5cf6',
        showSpinner: false,
    },
})
