<script setup lang="ts">
import {computed, provide} from 'vue';
import {Link} from '@inertiajs/vue3';
import type {Action} from './types';
import {Method} from "@inertiajs/core";

const props = defineProps<{
    actions: Action[],
    actionName: string,
    mode?: 'link' | 'button',
    provideOnly?: boolean,
}>()

const action = computed(() =>
    props.actions.find(a => a.name === props.actionName || a.title === props.actionName)
)

const isLink = computed(() => action.value && !action.value.async);
const url = computed(() => action.value?.url);
const method = computed(() => (action.value?.method?.toLowerCase() || 'get') as Method);

if (isLink && props.provideOnly) {
    provide('link', {
        url: url.value,
        method: method.value,
    });
}
</script>

<template>
    <Link v-if="isLink && !props.provideOnly && url" :href="url" :method="method" class="hover:underline text-primary">
        <slot/>
    </Link>
    <span v-else>
        <slot/>
    </span>
</template>
