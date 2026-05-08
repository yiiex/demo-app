<script setup lang="ts">
import {DetailItemProps} from "@js/components/ui/udetail/types.ts";
import {computed, inject} from "vue";

const props = defineProps<DetailItemProps>();
const model = inject<any>('model');
const attributeValue = computed(() => {
    return props.name ? model[props.name] : null;
});
</script>

<template>
    <div class="flex flex-row w-full border-b divide-main-600 last:border-b-0" v-if="attributeValue">
        <div class="bg-muted/50 p-3 w-1/2 xl:w-48 2xl:w-56">
            <slot name="label" :model="model">
                {{ props.label }}
            </slot>
        </div>
        <div class="p-3 flex-1 my-auto">
            <slot name="default" :model="model">
                {{ attributeValue }}
            </slot>
        </div>
    </div>
</template>

<style scoped>

</style>
