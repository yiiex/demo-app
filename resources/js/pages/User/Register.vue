<script setup>
import {ref} from "vue";
import {router, usePage} from "@inertiajs/vue3";
import CleanLayout from "@js/layouts/CleanLayout.vue";
import {Card, CardContent, CardDescription, CardHeader, CardTitle} from "@js/components/ui/card/index.ts";
import {UForm, UFormButton, UFormItem} from "@js/components/ui/uform/index.ts";
import {Input} from "@js/components/ui/input/index.ts";

const page = usePage();
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
    <div class="flex min-h-svh flex-col items-center justify-center gap-6 bg-muted/50 p-6 md:p-10">
        <div class="flex w-full max-w-sm flex-col gap-6">
            <Link href="/" class="flex items-center gap-2 self-center font-bold text-2xl">
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-primary text-primary-foreground">
                    <span class="text-lg font-bold">A</span>
                </div>
                {{ page.props.appName }}
            </Link>

            <Card class="shadow-lg">
                <CardHeader class="space-y-1 text-center">
                    <CardTitle class="text-2xl font-bold">
                        Create an account
                    </CardTitle>
                    <CardDescription>
                        Enter your details to get started
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <UForm url="/user/register" :model="model" @onResponse="successHandler">
                        <div class="space-y-4">
                            <UFormItem name="email" label="Email">
                                <Input v-model="model.email" type="email" placeholder="name@example.com"/>
                            </UFormItem>

                            <UFormItem name="password" label="Password">
                                <Input v-model="model.password" type="password"/>
                            </UFormItem>

                            <UFormItem name="password_confirm" label="Confirm Password">
                                <Input v-model="model.password_confirm" type="password"/>
                            </UFormItem>

                            <UFormButton type="submit" name="register" class="w-full">
                                Create account
                            </UFormButton>
                        </div>
                    </UForm>
                </CardContent>
            </Card>

            <div class="text-center text-sm text-muted-foreground">
                Already have an account?
                <Link href="/user/login" class="underline underline-offset-4 hover:text-primary transition-colors">
                    Sign in
                </Link>
            </div>
        </div>
    </div>
</template>

<style scoped>

</style>
