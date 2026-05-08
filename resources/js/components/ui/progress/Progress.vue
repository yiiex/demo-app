<script setup lang="ts">
import type { ProgressRootProps } from "reka-ui"
import type { HTMLAttributes } from "vue"
import { reactiveOmit } from "@vueuse/core"
import {
    ProgressIndicator,
    ProgressRoot,
} from "reka-ui"
import { cn } from '@/js/lib/utils'

const props = withDefaults(
    defineProps<ProgressRootProps & {
        class?: HTMLAttributes["class"]
        variant?: 'default' | 'secondary' | 'outline' | 'ghost' | 'destructive' | 'success'
    }>(),
    {
        modelValue: 0,
        variant: 'default',
    },
)

const delegatedProps = reactiveOmit(props, "class")

const variantClasses: Record<string, { root: string; indicator: string }> = {
    default: {
        root: 'bg-primary/20',
        indicator: 'bg-primary',
    },
    secondary: {
        root: 'bg-secondary',
        indicator: 'bg-foreground/20',
    },
    outline: {
        root: 'border border-input bg-transparent',
        indicator: 'bg-foreground/15',
    },
    ghost: {
        root: 'bg-transparent',
        indicator: 'bg-foreground/10',
    },
    destructive: {
        root: 'bg-destructive/20',
        indicator: 'bg-destructive',
    },
    success: {
        root: 'bg-emerald-500/20',
        indicator: 'bg-emerald-500',
    },
}
</script>

<template>
    <ProgressRoot
        data-slot="progress"
        v-bind="delegatedProps"
        :class="
      cn(
        'relative h-2 w-full overflow-hidden rounded-full',
        variantClasses[props.variant].root,
        props.class,
      )
    "
    >
        <ProgressIndicator
            data-slot="progress-indicator"
            :class="cn(
        'h-full w-full flex-1 transition-all rounded-full',
        variantClasses[props.variant].indicator,
      )"
            :style="`transform: translateX(-${100 - (props.modelValue ?? 0)}%);`"
        />
    </ProgressRoot>
</template>
