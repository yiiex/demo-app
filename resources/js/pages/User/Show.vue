<script setup>
import DashboardLayout from "@js/layouts/DashboardLayout.vue";
import {UDetail, UDetailItem} from "@js/components/ui/udetail/index.ts";
import {Badge} from "@js/components/ui/badge/index.ts";

defineOptions({
    layout: DashboardLayout,
});
const props = defineProps({
    user: null,
})
</script>

<template>
    <div class="grid grid-cols-2 gap-4">
        <UDetail :model="user">
            <UDetailItem label="ID" name="id"/>
            <UDetailItem label="Full Name" name="fullName"/>
            <UDetailItem label="E-mail" name="email"/>
            <UDetailItem label="Role" name="role">
                <template #default="{model}">
                    <Badge variant="secondary">{{ model.role }}</Badge>
                </template>
            </UDetailItem>
            <UDetailItem label="Created at" name="created_at"/>
            <UDetailItem label="Updated at" name="updated_at"/>
        </UDetail>
    </div>
</template>

<style scoped>

</style>
