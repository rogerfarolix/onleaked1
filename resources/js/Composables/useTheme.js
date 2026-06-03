import { ref } from 'vue'

// Onleaked is dark-only by design. Kept as a stable export so existing
// imports don't break, but there is no theme switching.
const dark = ref(true)

export function useTheme() {
    const toggle = () => {}
    return { dark, toggle }
}
