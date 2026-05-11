<script setup>
import {computed, inject, useSlots} from "vue";
import UserAvatar from "@js/components/user/UserAvatar.vue";

const props = defineProps({
    user: {
        type: Object,
        default: null,
    },
});

const slots = useSlots();

const hasDescription = computed(() => !!slots.description);
const hasRight = computed(() => !!slots.right);

const fullName = computed(() => {
    return props.user?.fullName || 'undefined';
});
const link = inject('link', null);
</script>

<template>
    <div class="flex items-center gap-2">
        <UserAvatar :user="user"/>
        <div class="flex-1 min-w-0">
            <div class="flex items-center gap-2">
                <slot name="name">
                    <Link v-if="link && link.url" :href="link.url" class="text-sm! font-medium truncate hover:underline">{{ fullName }}</Link>
                    <span v-else class="text-sm! font-medium truncate">{{ fullName }}</span>
                </slot>
                <slot v-if="hasRight" name="right"/>
            </div>
            <slot v-if="hasDescription" name="description"></slot>
        </div>
    </div>
</template>
