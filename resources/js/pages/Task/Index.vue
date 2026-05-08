<script setup>

import DashboardLayout from "@js/layouts/DashboardLayout.vue";
import {UTable, UTableColumn} from "@js/components/ui/utable/index.ts";
import {UActionList} from "@js/components/ui/uaction/index.ts";
import {UFormItem} from "@js/components/ui/uform/index.ts";
import {Input} from "@js/components/ui/input/index.ts";
import TaskStatus from "@js/pages/Task/Components/TaskStatus.vue";
import {Badge} from "@js/components/ui/badge/index.ts";
import UActionLink from "@js/components/ui/uaction/UActionLink.vue";

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
    <UTable :data="dataProvider" url="/task/index">
        <template #filter="{model}">
            <UFormItem name="search" :without-label="true">
                <Input v-model="model.search" placeholder="Start typing to search task..." class="w-80"/>
            </UFormItem>
        </template>
        <template #buttons>
            <UActionList :actions="actions"/>
        </template>
        <UTableColumn prop="id" label="ID"/>
        <UTableColumn prop="shortName" label="Name" cellClass="max-w-[200px] whitespace-normal">
            <template #default="{ row, index, provider }">
                <UActionLink :actions="provider.actions[index] || []"
                             actionName="show">{{ row.shortName }}
                </UActionLink>
            </template>
        </UTableColumn>
        <UTableColumn prop="status" label="Status">
            <template #default="{ row, index }">
                <TaskStatus :task="row"/>
            </template>
        </UTableColumn>
        <UTableColumn prop="estimated_time" label="Estimated time">
            <template #default="{ row, index }">
                <Badge variant="secondary">{{ row.estimated_time }}</Badge>
            </template>
        </UTableColumn>
        <UTableColumn prop="deadline" label="Deadline"/>
        <UTableColumn prop="created_at" label="Created at"/>
        <UTableColumn prop="updated_at" label="Updated at"/>
        <UTableColumn label="Actions">
            <template #default="{ row, index, provider }">
                <UActionList :actions="provider.actions[index]" mode="compact"/>
            </template>
        </UTableColumn>
    </UTable>
</template>

<style scoped>

</style>
