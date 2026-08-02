<script setup>
import {Badge} from "@js/components/ui/badge/index.ts";
import {computed} from "vue";
import {CircleIcon, LoaderIcon} from "@lucide/vue";
import {CircleCheckIcon} from "@lucide/vue";

const props = defineProps({
    task: {
        type: Object,
        required: true,
    },
});

const statusConfig = {
    pending: {
        variant: 'secondary',
        label: 'Pending',
        icon: CircleIcon,
        iconClass: 'h-3.5 w-3.5'
    },
    in_progress: {
        variant: 'outline',
        label: 'In Progress',
        icon: LoaderIcon,
        iconClass: 'h-3.5 w-3.5 animate-spin'
    },
    done: {
        variant: 'success',
        label: 'Done',
        icon: CircleCheckIcon,
        iconClass: 'h-3.5 w-3.5'
    }
};

const currentStatus = computed(() =>
        statusConfig[props.task?.status] || {
            variant: 'secondary',
            label: 'Unknown',
            icon: CircleIcon,
            iconClass: 'h-3.5 w-3.5'
        }
);
</script>

<template>
    <Badge :variant="currentStatus.variant" class="flex items-center gap-1.5">
        <component :is="currentStatus.icon" :class="currentStatus.iconClass" />
        {{ currentStatus.label }}
    </Badge>
</template>
