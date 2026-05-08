<script setup lang="ts">

import {Select, SelectContent, SelectItem, SelectTrigger, SelectValue} from "@js/components/ui/select";
import {SelectProps} from "@js/components/ui/uform/types.ts";
import {computed} from "vue";
const props = defineProps<SelectProps>();
const model = defineModel();
const valueLabel = computed(() => {
    if (!model.value) {
        return props.placeholder || 'Select item';
    }
    const labels = (Array.isArray(model.value) ? model.value : [model.value]).map(roleKey => {
        return props.list.find(r => r.key === roleKey)?.label;
    });

    return labels.join(', ');
});
</script>

<template>
    <Select v-model="model">
        <SelectTrigger class="w-full">
            <SelectValue placeholder="Select a role">{{ valueLabel}}</SelectValue>
        </SelectTrigger>
        <SelectContent>
            <SelectItem v-for="item in props.list || []" :value="item.key">{{ item.label }}</SelectItem>
        </SelectContent>
    </Select>
</template>

<style scoped>

</style>
