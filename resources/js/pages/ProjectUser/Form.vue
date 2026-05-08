<script setup>
import {Card} from "@js/components/ui/card";
import DashboardLayout from "@js/layouts/DashboardLayout.vue";
import {UForm} from "@js/components/ui/uform/index.ts";
import {UCombobox, UFormButton, UFormItem} from "@js/components/ui/uform/index.ts";
import {CardContent} from "@js/components/ui/card/index.ts";

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
        <Card>
            <CardContent>
                <UForm :url="saveUrl" :attributeLabels="form.attributeLabels" :safeAttributes="form.safeAttributes"
                       :model="form.model">
                    <template #default="{model}">
                        <UFormItem name="user_id" class="mb-3">
                            <UCombobox v-model="model.user_id" provider="user.autocomplete"/>
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
