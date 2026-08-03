<script setup>
import DashboardLayout from "@js/layouts/DashboardLayout.vue";
import {UDetail, UDetailItem} from "@js/components/ui/udetail/index.ts";
import {Card, CardContent, CardHeader, CardTitle} from "@js/components/ui/card/index.ts";
import {UTable, UTableColumn} from "@js/components/ui/utable/index.ts";
import {UActionList} from "@js/components/ui/uaction/index.ts";
import {Avatar, AvatarFallback, AvatarImage} from "@js/components/ui/avatar/index.ts";
import {UFormItem} from "@js/components/ui/uform/index.ts";
import {Input} from "@js/components/ui/input/index.ts";
import UserInfo from "@js/components/user/UserInfo.vue";

defineOptions({
    layout: DashboardLayout,
});
const props = defineProps({
    project: null,
    dataProvider: null,
    userActions: {
        type: Array,
        default: () => [],
    },
    currentUrl: {
        type: String,
        default: '/',
    },
})
</script>

<template>
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-3">
        <UDetail :model="project">
            <UDetailItem label="ID" name="id"/>
            <UDetailItem label="Name" name="name"/>
            <UDetailItem label="Created at" name="created_at"/>
            <UDetailItem label="Updated at" name="updated_at"/>
        </UDetail>
        <Card>
            <CardHeader>
                <CardTitle>Project Description</CardTitle>
            </CardHeader>
            <CardContent>
                <p v-html="project.description"></p>
            </CardContent>
        </Card>
        <div class="lg:col-span-2">
            <h2 class="text-xl font-semibold mb-3">Members</h2>
            <UTable :data="dataProvider" :url="currentUrl">
                <template #filter="{model}">
                    <UFormItem name="fullName" :without-label="true">
                        <Input v-model="model.fullName" placeholder="Start typing to search by name..." class="w-80"/>
                    </UFormItem>
                </template>
                <template #buttons>
                    <UActionList :actions="userActions"/>
                </template>
                <UTableColumn prop="id" label="ID"/>
                <UTableColumn prop="fullName" label="User">
                    <template #default="{ row }">
                        <UserInfo :user="row.user">
                            <template #description>
                                <div class="text-xs text-muted-foreground">{{ row.created_at }}</div>
                            </template>
                        </UserInfo>
                    </template>
                </UTableColumn>
                <UTableColumn prop="created_at" label="Member since"/>
                <UTableColumn label="Actions">
                    <template #default="{ row, index }">
                        <UActionList :actions="dataProvider.actions[index]"/>
                    </template>
                </UTableColumn>
            </UTable>
        </div>
    </div>
</template>

<style scoped>

</style>
