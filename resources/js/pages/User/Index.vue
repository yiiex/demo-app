<script setup>

import DashboardLayout from "@js/layouts/DashboardLayout.vue";
import {UTable, UTableColumn} from "@js/components/ui/utable/index.ts";
import {Badge} from "@js/components/ui/badge/index.ts";
import {UActionList} from "@js/components/ui/uaction/index.ts";
import {UFormItem} from "@js/components/ui/uform/index.ts";
import {Input} from "@js/components/ui/input/index.ts";
import {Avatar, AvatarImage, AvatarFallback} from "@js/components/ui/avatar/index.ts";

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
            <template #default="{ row }">
                <div class="flex gap-2">
                    <Avatar>
                        <AvatarImage :src="row.avatar" />
                        <AvatarFallback>{{ row.fullName }}</AvatarFallback>
                    </Avatar>
                    <div class="my-auto">{{ row.fullName }}</div>
                </div>
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
            <template #default="{ row, index }">
                <UActionList :actions="users.actions[index]" mode="compact"/>
            </template>
        </UTableColumn>
    </UTable>
</template>

<style scoped>

</style>
