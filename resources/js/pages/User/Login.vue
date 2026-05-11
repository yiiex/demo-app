<script setup>
import {ref} from "vue";
import {router, usePage} from "@inertiajs/vue3";
import CleanLayout from "@js/layouts/CleanLayout.vue";
import {Card, CardContent, CardDescription, CardHeader, CardTitle} from "@js/components/ui/card/index.ts";
import {UForm, UFormButton, UFormItem} from "@js/components/ui/uform/index.ts";
import {Input} from "@js/components/ui/input/index.ts";
import {Switch} from "@js/components/ui/switch/index.ts";

const page = usePage();
defineOptions({
    layout: CleanLayout
});
const model = ref({
    email: null,
    password: null,
    remember: false,
});
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
                        Welcome back
                    </CardTitle>
                    <CardDescription>
                        Enter your credentials to access your account
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <UForm url="/user/login" :model="model">
                        <div class="space-y-4">
                            <UFormItem name="email" label="Email">
                                <Input v-model="model.email" type="email" placeholder="name@example.com" />
                            </UFormItem>

                            <UFormItem name="password" label="Password">
                                <Input v-model="model.password" type="password" placeholder="••••••••" />
                            </UFormItem>

                            <UFormItem
                                name="remember"
                                label="Remember me"
                                label-position="after"
                                :inline="true"
                            >
                                <Switch v-model="model.remember" />
                            </UFormItem>

                            <UFormButton type="submit" name="login" class="w-full">
                                Sign in
                            </UFormButton>
                        </div>
                    </UForm>
                </CardContent>
            </Card>

            <div class="text-center text-sm text-muted-foreground">
                Don't have an account?
                <Link href="/user/register" class="underline underline-offset-4 hover:text-primary transition-colors">
                    Sign up
                </Link>
            </div>
        </div>
    </div>
</template>

<style scoped>

</style>
