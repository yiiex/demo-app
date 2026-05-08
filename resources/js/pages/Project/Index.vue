<script setup>

import DashboardLayout from "@js/layouts/DashboardLayout.vue";
import {UTable, UTableColumn} from "@js/components/ui/utable/index.ts";
import {Badge} from "@js/components/ui/badge/index.ts";
import {UActionList} from "@js/components/ui/uaction/index.ts";
import {UFormItem} from "@js/components/ui/uform/index.ts";
import {Input} from "@js/components/ui/input/index.ts";

defineOptions({
    layout: DashboardLayout,
});
defineProps({
    dataProvider: {
        type: Object,
        required: true
    },
    actions: {
        type: Array,
        required: false,
    },
});
</script>

<template>
    <UTable :data="dataProvider" url="/project/index">
        <template #filter="{model}">
            <UFormItem name="search" :without-label="true">
                <Input v-model="model.search" placeholder="Start typing to search project..." class="w-80"/>
            </UFormItem>
        </template>
        <template #buttons>
            <UActionList :actions="actions"/>
        </template>
        <UTableColumn prop="id" label="ID"/>
        <UTableColumn prop="name" label="Name"/>
        <UTableColumn prop="users_count" label="Members count"/>
        <UTableColumn prop="created_at" label="Created at"/>
        <UTableColumn label="Actions">
            <template #default="{ row, index }">
                <UActionList :actions="dataProvider.actions[index]" mode="compact"/>
            </template>
        </UTableColumn>
    </UTable>
</template>

<style scoped>

</style>
