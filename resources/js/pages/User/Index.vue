<script setup>

import DashboardLayout from "@js/layouts/DashboardLayout.vue";
import {UTable, UTableColumn} from "@js/components/ui/utable/index.ts";
import {Badge} from "@js/components/ui/badge/index.ts";
import {UActionLink, UActionList} from "@js/components/ui/uaction/index.ts";
import {UFormItem} from "@js/components/ui/uform/index.ts";
import {Input} from "@js/components/ui/input/index.ts";
import UserInfo from "@js/components/user/UserInfo.vue";

defineOptions({
    layout: DashboardLayout,
});
defineProps({
    users: {
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
    <UTable :data="users" url="/user/index">
        <template #filter="{model}">
            <UFormItem name="fullName" :without-label="true">
                <Input v-model="model.fullName" placeholder="Start typing to search by name..." class="w-80"/>
            </UFormItem>
        </template>
        <template #buttons>
            <UActionList :actions="actions"/>
        </template>
        <UTableColumn prop="id" label="ID"/>
        <UTableColumn prop="fullName" label="User">
            <template #default="{ row, index, provider }">
                <UActionLink :actions="provider.actions[index]" actionName="show" :provideOnly="true">
                    <UserInfo :user="row"/>
                </UActionLink>
            </template>
        </UTableColumn>
        <UTableColumn prop="email" label="E-mail"/>
        <UTableColumn prop="role" label="Role">
            <template #default="{ row }">
                <Badge variant="secondary">{{ row.role }}</Badge>
            </template>
        </UTableColumn>
        <UTableColumn prop="created_at" label="Created at"/>
        <UTableColumn label="Actions">
            <template #default="{ row, index, provider }">
                <UActionList :actions="users.actions[index]" mode="compact"/>
            </template>
        </UTableColumn>
    </UTable>
</template>

<style scoped>

</style>
