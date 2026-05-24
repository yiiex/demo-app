<script setup>
import {Card} from "@js/components/ui/card";
import DashboardLayout from "@js/layouts/DashboardLayout.vue";
import {UForm} from "@js/components/ui/uform/index.ts";
import {UCombobox, UDatePicker, UFormButton, UFormItem} from "@js/components/ui/uform/index.ts";
import {Input} from "@js/components/ui/input/index.ts";
import {CardContent} from "@js/components/ui/card/index.ts";
import {Textarea} from "@js/components/ui/textarea/index.ts";

defineOptions({
    layout: DashboardLayout,
});
const props = defineProps({
    form: null,
    saveUrl: null,
});
</script>

<template>
    <UForm :url="saveUrl" :attributeLabels="form.attributeLabels" :safeAttributes="form.safeAttributes"
           :model="form.model">
        <template #default="{model}">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-3">
                <Card>
                    <CardContent>
                        <UFormItem name="project_id" class="mb-3">
                            <UCombobox v-model="model.project_id" provider="project.autocomplete"/>
                        </UFormItem>
                        <UFormItem name="name" class="mb-3">
                            <Input v-model="model.name"/>
                        </UFormItem>
                        <UFormItem name="description" class="mb-3">
                            <Textarea v-model="model.description" placeholder="Task description"/>
                        </UFormItem>
                    </CardContent>
                </Card>
                <div class="">
                    <Card>
                        <CardContent>
                            <UFormItem name="estimated_time" class="mb-3">
                                <Input v-model="model.estimated_time" placeholder="format 10h 20m"/>
                            </UFormItem>
                            <UFormItem name="deadline" class="mb-3">
                                <UDatePicker v-model="model.deadline" type="date-time"/>
                            </UFormItem>
                            <UFormItem name="rawUsers" class="mb-3">
                                <UCombobox v-model="model.rawUsers" provider="user.autocomplete" multiple/>
                            </UFormItem>
                        </CardContent>
                    </Card>
                </div>
            </div>
            <UFormButton type="submit" name="save">Save</UFormButton>
        </template>
    </UForm>
</template>

<style scoped>

</style>
