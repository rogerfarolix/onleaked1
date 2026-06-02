import { ref } from 'vue'

const dark = ref(typeof window !== 'undefined' ? localStorage.getItem('theme') !== 'light' : true)

export function useTheme() {
    const toggle = () => {
        dark.value = !dark.value
        localStorage.setItem('theme', dark.value ? 'dark' : 'light')
        document.documentElement.classList.toggle('dark', dark.value)
    }

    return { dark, toggle }
}
