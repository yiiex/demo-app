<script setup lang="ts">
import { computed, inject, provide, ref, watch } from 'vue'
import { toast } from 'vue-sonner'
import { router } from '@inertiajs/vue3'
import type { UFormProps, UFormEmits, FormContext } from './types'
import axios from "axios";

const props = withDefaults(defineProps<UFormProps>(), {
    errors: null,
    method: 'POST',
    model: null,
    modelPath: null,
    safeAttributes: () => [],
    attributeLabels: () => ({}),
})

const emit = defineEmits<UFormEmits>()

const formErrors = ref<Record<string, string[]>>(props.errors || {})

watch(() => props.errors, (newErrors) => {
    formErrors.value = newErrors || {}
})

const injectedModel = inject<Record<string, any>>('model', null as any)
const model = computed<Record<string, any>>(() => {
    return props.model ? props.model : injectedModel?.[props.modelPath!]
})

provide<FormContext>('form', {
    model,
    safeAttributes: props.safeAttributes,
    attributeLabels: props.attributeLabels,
    errors: formErrors,
    setErrors: (errors) => {
        formErrors.value = errors || {}
    },
    clearErrors: () => {
        formErrors.value = {}
    },
})

const submit = async (name: string | null = null): Promise<any> => {
    emit('submit')
    return new Promise(async (resolve, reject) => {
        try {
            const response = await axios({
                method: props.method,
                url: props.url,
                data: { ...model.value, $button: name || 'submit' },
            })
            formErrors.value = {}
            if (response.data.message) {
                toast.success(response.data.message)
            }
            emit('onResponse', response.data)
            if (response.data.redirectUrl) {
                router.visit(response.data.redirectUrl)
            }
            resolve(response.data)
        } catch (error: any) {
            const errorData = error.response?.data || { message: error.message }
            if (error.response?.status === 422 && error.response.data.errors) {
                formErrors.value = error.response.data.errors
            }
            if (error.response?.data?.message) {
                toast.error(error.response.data.message)
            }
            emit('onResponseError', errorData)
            reject(error)
        } finally {
            emit('onResponseFinal')
        }
    })
}

provide('formSubmit', submit)

defineExpose({
    submit,
    formErrors,
})
</script>

<template>
    <form
        :action="url"
        @submit.prevent="submit()"
        class="u-form"
    >
        <slot :model="model"></slot>
    </form>
</template>

<style scoped>
.u-form {
    @apply w-full;
}
</style>
