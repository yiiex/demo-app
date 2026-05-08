import {onMounted, onUnmounted, ref, type Ref} from 'vue'

export type Theme = 'light' | 'dark' | 'system'

const currentTheme: Ref<Theme> = ref('system');
const appliedTheme: Ref<'light' | 'dark'> = ref('light');
let isInitialized = false;
let mediaQueryListener: ((e: MediaQueryListEvent) => void) | null = null;

export function useTheme() {
    const getStoredTheme = (): Theme | null => {
        if (typeof window === 'undefined') return null
        const stored = localStorage.getItem('theme')
        return stored === 'light' || stored === 'dark' || stored === 'system' ? stored : null
    };

    const setStoredTheme = (theme: Theme): void => {
        if (typeof window === 'undefined') return
        localStorage.setItem('theme', theme)
    };

    const getSystemTheme = (): 'light' | 'dark' => {
        if (typeof window === 'undefined') return 'light'
        return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light'
    };

    const applyTheme = (theme: Theme): void => {
        currentTheme.value = theme

        let actualTheme: 'light' | 'dark'
        if (theme === 'system') {
            actualTheme = getSystemTheme()
        } else {
            actualTheme = theme
        }

        appliedTheme.value = actualTheme

        document.documentElement.classList.remove('light', 'dark')
        document.documentElement.classList.add(actualTheme)
        setStoredTheme(theme)
    };

    const setTheme = (theme: Theme): void => {
        applyTheme(theme)
    };

    const toggleTheme = (): void => {
        if (currentTheme.value === 'dark') {
            applyTheme('light')
        } else if (currentTheme.value === 'light') {
            applyTheme('dark')
        } else {
            applyTheme('dark')
        }
    };

    const init = (): void => {
        if (isInitialized) {
            return;
        } else {
            isInitialized = true;
        }

        const preferred = getStoredTheme() || 'system';
        applyTheme(preferred);

        const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
        mediaQueryListener = (e: MediaQueryListEvent) => {
            if (currentTheme.value === 'system') {
                const newTheme = e.matches ? 'dark' : 'light'
                appliedTheme.value = newTheme
                document.documentElement.classList.remove('light', 'dark')
                document.documentElement.classList.add(newTheme)
            }
        };
        mediaQuery.addEventListener('change', mediaQueryListener);
    };

    onMounted(() => {
        init();
    });

    onUnmounted(() => {
        if (mediaQueryListener) {
            window.matchMedia('(prefers-color-scheme: dark)').removeEventListener('change', mediaQueryListener)
        }
    });

    return {
        currentTheme,
        appliedTheme,
        setTheme,
        toggleTheme
    }
}
