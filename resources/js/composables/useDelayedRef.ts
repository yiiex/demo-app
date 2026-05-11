import { computed, ref } from 'vue'

export function useDelayedRef(initial: boolean, delay: number = 200) {
    const inner = ref(initial)
    let timer: ReturnType<typeof setTimeout> | null = null

    return computed({
        get: () => inner.value,
        set: (val: boolean) => {
            if (timer) {
                clearTimeout(timer);
                timer = null;
            }

            if (val) {
                inner.value = true;
            } else {
                timer = setTimeout(() => {
                    inner.value = false;
                    timer = null;
                }, delay);
            }
        }
    })
}

export function useDelayedLoading(keys: string[], delay: number = 200) {
    const loadings = Object.fromEntries(
        keys.map(key => [key, useDelayedRef(false, delay)])
    ) as Record<string, ReturnType<typeof useDelayedRef>>

    const setLoading = (key: string, value: boolean) => {
        if (loadings[key]) {
            loadings[key].value = value
        }
    }

    const isLoading = (key: string) => loadings[key]?.value ?? false

    const anyLoading = computed(() =>
        Object.values(loadings).some(l => l.value)
    )

    return {
        loadings,
        setLoading,
        isLoading,
        anyLoading,
    }
}
