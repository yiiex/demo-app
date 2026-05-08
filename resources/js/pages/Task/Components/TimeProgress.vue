<script setup lang="ts">
import { computed } from 'vue'
import { Progress } from '@js/components/ui/progress'

const props = defineProps<{
    value: number,
    max?: number,
    progressClass?: string,
    variant?: 'default' | 'secondary' | 'outline' | 'ghost' | 'destructive' | 'success'
}>()

const textColor = computed(() => {
    const colors: Record<string, string> = {
        default: 'text-primary-foreground',
        secondary: 'text-secondary-foreground',
        outline: 'text-foreground',
        ghost: 'text-foreground',
        destructive: 'text-destructive-foreground',
        success: 'text-emerald-900 dark:text-emerald-850',
    }
    return colors[props.variant || 'default']
})
</script>

<template>
    <div class="relative w-full">
        <Progress
            :model-value="Math.min(value, 100)"
            :max="max"
            class="h-3"
            :class="progressClass"
            :variant="variant || 'default'"
        />
        <span
            class="absolute inset-0 flex items-center justify-center text-[10px] font-medium"
            :class="textColor"
        >
            <slot>{{ value }}</slot>
        </span>
    </div>
</template>
