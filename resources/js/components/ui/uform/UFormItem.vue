<script setup lang="ts">
import {computed, ComputedRef, inject, provide} from "vue"
import { Label } from '@/js/components/ui/label'
import type { UFormItemProps } from "./types"
import type { FormContext } from "../uform/types"

const props = withDefaults(defineProps<UFormItemProps>(), {
    label: null,
    name: null,
    errors: null,
    showErrorText: true,
    inline: false,
    labelPosition: 'before',
    withoutLabel: false,
})

const form = inject<FormContext>('form', {} as FormContext)

const fieldErrors = computed<string[]>(() => {
    if (props.errors !== null) {
        return props.errors
    }
    if (props.name && form.errors?.value?.[props.name]) {
        return form.errors.value[props.name]
    }
    return []
})

const hasErrors = computed<boolean>(() => {
    return fieldErrors.value.length > 0
})

provide<ComputedRef<boolean>>('hasErrors', hasErrors)

const cLabel = computed<string | null>(() => {
    if (props.withoutLabel) {
        return null
    }
    if (props.label) {
        return props.label
    }
    return form.attributeLabels?.[props.name!] ?? null
})

const visible = computed<boolean>(() => {
    if ((form.safeAttributes || []).length > 0) {
        return form.safeAttributes.includes(props.name!)
    }
    return true
})
</script>

<template>
    <div v-if="visible">
        <div :class="{ 'inline-flex my-auto gap-2': inline }">
            <Label v-if="cLabel && labelPosition === 'before'" :class="{ 'mb-2': !inline }">
                {{ cLabel }}
            </Label>
            <slot />
            <Label v-if="cLabel && labelPosition === 'after'">
                {{ cLabel }}
            </Label>
        </div>
        <div v-if="showErrorText && hasErrors" class="mt-1 text-sm text-red-600 dark:text-red-500">
            <div v-for="error in fieldErrors" :key="error">{{ error }}</div>
        </div>
    </div>
</template>
