<script setup lang="ts">
import { computed, inject, ref } from "vue"
import { router } from "@inertiajs/vue3"
import { Spinner } from "@js/components/ui/spinner/index.ts"
import { Button } from "@js/components/ui/button/index.ts"
import type { UFormButtonProps, UFormButtonEmits } from "./types"

const emits = defineEmits<UFormButtonEmits>()
const props = withDefaults(defineProps<UFormButtonProps>(), {
    name: 'submit',
    color: 'primary',
    type: 'button',
    size: 'default',
    loading: false,
    disabled: false,
    plain: false,
    href: null,
})

const ownLoading = ref(false)

const isButtonDisabled = computed<boolean>(() => {
    return props.disabled || props.loading || ownLoading.value
})

const formSubmit = inject<(name: string) => Promise<any>>('formSubmit', null as any)

const clickHandler = (event: MouseEvent) => {
    if (isButtonDisabled.value) {
        return
    }
    emits('click', event)
    if (props.href) {
        router.get(props.href)
        return
    }
    if (props.type === 'submit' && formSubmit) {
        ownLoading.value = true
        formSubmit(props.name).finally(() => {
            ownLoading.value = false
        })
    }
}
</script>

<template>
    <Button :type="type" :disabled="isButtonDisabled" @click.prevent="clickHandler">
        <Spinner v-if="loading || ownLoading" />
        <span class="me-2" v-else-if="$slots.icon">
            <slot name="icon"></slot>
        </span>
        <slot/>
    </Button>
</template>

<style scoped>

</style>
