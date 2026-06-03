import { createApp, h } from 'vue'
import { createInertiaApp } from '@inertiajs/vue3'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers'
import { ZiggyVue } from 'ziggy-js'
import CookieBanner from './Components/CookieBanner.vue'

createInertiaApp({
    title: (title) => title ? `${title} Onleaked` : 'Onleaked Renseignement cybersécurité',
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        const ziggy = props.initialPage.props.ziggy ?? {}

        createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue, ziggy)
            .mount(el)

        // Mount the cookie-consent banner once, globally (outside the Inertia app
        // so it persists across page visits).
        const cookieEl = document.createElement('div')
        document.body.appendChild(cookieEl)
        createApp(CookieBanner).mount(cookieEl)
    },
    progress: {
        color: '#c8403a',
        showSpinner: false,
    },
})
