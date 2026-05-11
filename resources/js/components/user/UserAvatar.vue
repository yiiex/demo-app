<script setup lang="ts">
import {User} from "@js/types/user";
import {Avatar, AvatarFallback, AvatarImage} from "@js/components/ui/avatar";
import {computed} from "vue";

const props = defineProps<{
    user: User
}>();

const fullName = computed(() => {
    return props.user.fullName ?? 'undefined';
});

const initials = computed(() => {
    return fullName.value
        .split(' ')
        .map(word => word.charAt(0))
        .join('')
        .toUpperCase()
        .slice(0, 2);
});

const avatarClasses = computed(() => {
    const colors = [
        { bg: 'bg-red-400', text: 'text-red-900' },
        { bg: 'bg-orange-400', text: 'text-orange-900' },
        { bg: 'bg-amber-400', text: 'text-amber-900' },
        { bg: 'bg-yellow-400', text: 'text-yellow-900' },
        { bg: 'bg-lime-400', text: 'text-lime-900' },
        { bg: 'bg-green-400', text: 'text-green-900' },
        { bg: 'bg-emerald-400', text: 'text-emerald-900' },
        { bg: 'bg-teal-400', text: 'text-teal-900' },
        { bg: 'bg-cyan-400', text: 'text-cyan-900' },
        { bg: 'bg-sky-400', text: 'text-sky-900' },
        { bg: 'bg-blue-400', text: 'text-blue-900' },
        { bg: 'bg-indigo-400', text: 'text-indigo-900' },
        { bg: 'bg-violet-400', text: 'text-violet-900' },
        { bg: 'bg-purple-400', text: 'text-purple-900' },
        { bg: 'bg-fuchsia-400', text: 'text-fuchsia-900' },
        { bg: 'bg-pink-400', text: 'text-pink-900' },
        { bg: 'bg-rose-400', text: 'text-rose-900' },
    ];

    let hash = 0;
    for (let i = 0; i < fullName.value.length; i++) {
        hash = fullName.value.charCodeAt(i) + ((hash << 5) - hash);
    }

    const colorIndex = Math.abs(hash) % colors.length;
    const { bg, text } = colors[colorIndex];

    return [bg, text, 'text-xs', 'font-medium', 'select-none', 'leading-none', 'hover:no-underline!'];
});
</script>

<template>
    <Avatar class="overflow-hidden hover:no-underline!">
        <AvatarImage v-if="user.avatar" :src="user.avatar" :alt="user.name"/>
        <AvatarFallback :class="avatarClasses">
            {{ initials }}
        </AvatarFallback>
    </Avatar>
</template>
