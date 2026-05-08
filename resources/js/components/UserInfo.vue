<script setup>
import {Avatar, AvatarFallback, AvatarImage} from "@js/components/ui/avatar/index.ts";
import {computed, useSlots} from "vue";
import {useAuth} from "@js/composables/useAuth.ts";

const props = defineProps({
    user: {
        type: Object,
        default: null,
    },
});

const slots = useSlots();
const {defaultAvatar} = useAuth();

const hasDescription = computed(() => !!slots.description);
const hasRight = computed(() => !!slots.right);

const fullName = computed(() => {
    return props.user?.fullName || 'undefined';
});
</script>

<template>
    <div class="flex items-center gap-2">
        <Avatar>
            <AvatarImage :src="user?.avatar || defaultAvatar"/>
            <AvatarFallback>{{ fullName }}</AvatarFallback>
        </Avatar>
        <div class="flex-1 min-w-0">
            <div class="flex items-center gap-2">
                <slot name="name">
                    <span class="text-sm! font-medium truncate">{{ fullName }}</span>
                </slot>
                <slot v-if="hasRight" name="right"/>
            </div>
            <slot v-if="hasDescription" name="description"></slot>
        </div>
    </div>
</template>
