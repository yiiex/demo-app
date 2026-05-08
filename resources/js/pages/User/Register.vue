<script setup>
import {ref} from "vue";
import {router} from "@inertiajs/vue3";
import CleanLayout from "@js/layouts/CleanLayout.vue";
import {Card, CardContent, CardHeader, CardTitle} from "@js/components/ui/card/index.ts";
import {UForm, UFormButton, UFormItem} from "@js/components/ui/uform/index.ts";
import {Input} from "@js/components/ui/input/index.ts";

defineOptions({
    layout: CleanLayout
});
const model = ref({
    email: null,
    password: null,
    password_confirm: null,
});
const successHandler = (data) => {
    if (data.success) {
        router.get('/');
    }
}
</script>

<template>
    <div class="flex min-h-svh flex-col items-center justify-center gap-6 p-6 md:p-10">
        <div class="flex w-full max-w-sm flex-col gap-6">
            <div class="flex flex-col gap-6">
                <Card>
                    <CardHeader class="text-center">
                        <CardTitle class="text-xl">
                            Welcome back
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        <UForm url="/user/login" :model="model" @onResponse="successHandler">
                            <UFormItem name="email" label="Email" class="mb-3">
                                <Input v-model="model.email"/>
                            </UFormItem>
                            <UFormItem name="password" label="Password" class="mb-3">
                                <Input v-model="model.password" type="password"/>
                            </UFormItem>
                            <UFormItem name="password_confirm" label="Password confirm" class="mb-3">
                                <Input v-model="model.password_confirm" type="password"/>
                            </UFormItem>
                            <UFormButton type="submit" name="login">Register</UFormButton>
                        </UForm>
                    </CardContent>
                </Card>
            </div>
        </div>
    </div>
</template>

<style scoped>

</style>
