<script setup lang="ts">
import {DetailProps} from "@js/components/ui/udetail/types.ts";
import {provide} from "vue";

const props = defineProps<DetailProps>();
provide('model', props.model);
</script>

<template>
    <div class="flex flex-col rounded-md overflow-hidden outline outline-offset-2 outline-neutral-200 dark:outline-neutral-900 text-sm">
        <slot/>
    </div>
</template>

<style scoped>

</style>
