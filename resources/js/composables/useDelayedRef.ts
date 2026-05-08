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
