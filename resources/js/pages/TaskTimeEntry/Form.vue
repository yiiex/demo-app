<script setup>
import {Card, CardContent} from "@js/components/ui/card";
import DashboardLayout from "@js/layouts/DashboardLayout.vue";
import {UForm, UDatePicker, UFormButton, UFormItem} from "@js/components/ui/uform/index.ts";
import {Input} from "@js/components/ui/input/index.ts";
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
    <div class="grid grid-cols-2 gap-4">
        <UForm :url="saveUrl" :attributeLabels="form.attributeLabels" :safeAttributes="form.safeAttributes"
               :model="form.model">
            <template #default="{model}">
                <Card>
                    <CardContent>
                        <UFormItem name="date" label="Date" class="mb-3">
                            <UDatePicker v-model="model.date"/>
                        </UFormItem>

                        <UFormItem name="duration" label="Duration" class="mb-3">
                            <Input v-model="model.duration" placeholder="format 10h 20m"/>
                        </UFormItem>

                        <UFormItem name="description" label="Description" class="mb-3">
                            <Textarea v-model="model.description" placeholder="What did you work on?"/>
                        </UFormItem>
                        <UFormButton type="submit" name="save" class="mt-4">Save</UFormButton>
                    </CardContent>
                </Card>
            </template>
        </UForm>
    </div>
</template>

<style scoped>

</style>
