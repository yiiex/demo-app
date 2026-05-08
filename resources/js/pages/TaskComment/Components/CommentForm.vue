<script setup lang="ts">
import {UForm, UFormButton, UFormItem} from "@js/components/ui/uform";
import {inject} from "vue";
import {DataSourceContext} from "@js/components/ui/udata/types.ts";
import {Textarea} from "@js/components/ui/textarea";

const props = defineProps<{
    form: any
    saveUrl: string
    withRefresh?: boolean
}>()

const dataSource = inject<DataSourceContext | undefined>('dataSource', undefined);
const handleResponse = (data: any) => {
    if (props.withRefresh && data.success && dataSource) {
        dataSource.refresh();
        props.form.model.comment = '';
    }
}
</script>

<template>
    <UForm :url="saveUrl" :attributeLabels="form.attributeLabels" :safeAttributes="form.safeAttributes"
           :model="form.model" @onResponse="handleResponse">
        <template #default="{model}">
            <div class="flex flex-col gap-2">
                <UFormItem :withoutLabel="true" name="comment">
                    <Textarea v-model="model.comment" placeholder="Write a comment..." class="w-full"/>
                </UFormItem>
                <div class="">
                    <UFormButton type="submit" name="save">Send</UFormButton>
                </div>
            </div>
        </template>
    </UForm>
</template>

<style scoped>

</style>
