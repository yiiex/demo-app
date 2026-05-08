<script setup>
import {Card} from "@js/components/ui/card";
import DashboardLayout from "@js/layouts/DashboardLayout.vue";
import {UForm} from "@js/components/ui/uform/index.ts";
import {UFormButton, UFormItem} from "@js/components/ui/uform/index.ts";
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
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <Card>
            <CardContent>
                <UForm :url="saveUrl" :attributeLabels="form.attributeLabels" :safeAttributes="form.safeAttributes" :model="form.model">
                    <template #default="{model}">
                        <UFormItem name="name" class="mb-3">
                            <Input v-model="model.name"/>
                        </UFormItem>
                        <UFormItem name="description" class="mb-3">
                            <Textarea v-model="model.description" placeholder="Project description" />
                        </UFormItem>
                        <UFormButton type="submit" name="save">Save</UFormButton>
                    </template>
                </UForm>
            </CardContent>
        </Card>
    </div>
</template>

<style scoped>

</style>
