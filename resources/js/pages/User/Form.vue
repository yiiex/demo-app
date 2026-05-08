<script setup>
import {Card} from "@js/components/ui/card";
import DashboardLayout from "@js/layouts/DashboardLayout.vue";
import {UForm} from "@js/components/ui/uform/index.ts";
import {UFormButton, UFormItem, USelect} from "@js/components/ui/uform/index.ts";
import {Input} from "@js/components/ui/input/index.ts";
import {CardContent} from "@js/components/ui/card/index.ts";
import {router} from "@inertiajs/vue3";

defineOptions({
    layout: DashboardLayout,
});
const props = defineProps({
    form: null,
    saveUrl: null,
});

const roleList = [
    {key: 'user', label: 'User'},
    {key: 'admin', label: 'Admin'},
];
</script>

<template>
    <div class="grid grid-cols-2 gap-4">
        <Card>
            <CardContent>
                <UForm :url="saveUrl" :attributeLabels="form.attributeLabels" :safeAttributes="form.safeAttributes" :model="form.model">
                    <template #default="{model}">
                        <UFormItem name="first_name" class="mb-3">
                            <Input v-model="model.first_name"/>
                        </UFormItem>
                        <UFormItem name="last_name" class="mb-3">
                            <Input v-model="model.last_name"/>
                        </UFormItem>
                        <UFormItem name="role" class="mb-3">
                            <USelect v-model="model.role" :list="roleList"/>
                        </UFormItem>
                        <UFormItem name="email" class="mb-3">
                            <Input v-model="model.email"/>
                        </UFormItem>
                        <UFormItem name="password" class="mb-3">
                            <Input v-model="model.password" type="password"/>
                        </UFormItem>
                        <UFormItem name="password_confirm" class="mb-3">
                            <Input v-model="model.password_confirm" type="password"/>
                        </UFormItem>
                        <UFormButton type="submit" name="save">Save</UFormButton>
                    </template>
                </UForm>
            </CardContent>
        </Card>
        <Card v-if="form.model.id">
        </Card>
    </div>
</template>

<style scoped>

</style>
