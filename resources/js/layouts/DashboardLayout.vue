<script setup>
import {Head, usePage} from "@inertiajs/vue3";
import {computed} from "vue";
import DashboardSidebar from "@/js/layouts/DashboardSidebar.vue";
import {SidebarInset, SidebarProvider, SidebarTrigger} from "@/js/components/ui/sidebar/index.ts";
import {
    Breadcrumb,
    BreadcrumbItem,
    BreadcrumbLink,
    BreadcrumbList,
    BreadcrumbPage,
    BreadcrumbSeparator
} from "@/js/components/ui/breadcrumb/index.ts";
import {Separator} from "@/js/components/ui/separator/index.ts";
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle
} from "@js/components/ui/dialog/index.ts";
import {Button} from "@js/components/ui/button/index.ts";
import {Spinner} from "@js/components/ui/spinner/index.ts";
import {Toaster} from "@js/components/ui/sonner/index.ts";
import {useAuth} from "@js/composables/useAuth.ts";
import {useTheme} from "@js/composables/useTheme.ts";
import DebugPanel from "@js/components/ui/debug/DebugPanel.vue";


const {logout, logoutDialog, logoutDialogLoading} = useAuth();
const {currentTheme} = useTheme();
const page = usePage();

const breadcrumbs = computed(() => page.props.breadcrumbs || []);
const pageTitle = computed(() => page.props.title || '');
</script>

<template>
    <SidebarProvider>
        <DebugPanel />
        <Head :title="pageTitle"/>
        <Toaster position="top-center" :theme="currentTheme"/>
        <Dialog v-model:open="logoutDialog">
            <DialogContent class="sm:max-w-[425px]">
                <DialogHeader>
                    <DialogTitle>Are you sure you want to log out?</DialogTitle>
                    <DialogDescription>
                        You will be redirected to the login page. You'll need to sign in again to access your account.
                    </DialogDescription>
                </DialogHeader>
                <DialogFooter class="gap-2">
                    <Button variant="outline" @click="logoutDialog = false">
                        Cancel
                    </Button>
                    <Button variant="destructive" @click="logout" :disabled="logoutDialogLoading">
                        <Spinner v-if="logoutDialogLoading"/>
                        Log out
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
        <DashboardSidebar/>
        <SidebarInset>
            <header
                class="flex h-16 shrink-0 items-center justify-between gap-2 transition-[width,height] ease-linear group-has-data-[collapsible=icon]/sidebar-wrapper:h-12 px-4">
                <!-- Левая часть: иконка + H1 -->
                <div class="flex items-center gap-2">
                    <SidebarTrigger class="-ml-1"/>
                    <Separator
                        orientation="vertical"
                        class="h-4!"
                    />
                    <h1 class="text-lg font-semibold tracking-tight">
                        {{ pageTitle || 'Dashboard' }}
                    </h1>
                </div>

                <!-- Правая часть: хлебные крошки -->
                <Breadcrumb v-if="(breadcrumbs || []).length > 0">
                    <BreadcrumbList>
                        <template v-for="(crumb, index) in breadcrumbs" :key="index">
                            <BreadcrumbItem>
                                <BreadcrumbPage v-if="index === breadcrumbs.length - 1">
                                    {{ crumb.title }}
                                </BreadcrumbPage>
                                <BreadcrumbLink v-else as-child>
                                    <Link :href="crumb.url" class="hover:underline">
                                        {{ crumb.title }}
                                    </Link>
                                </BreadcrumbLink>
                            </BreadcrumbItem>
                            <BreadcrumbSeparator v-if="index < breadcrumbs.length - 1"/>
                        </template>
                    </BreadcrumbList>
                </Breadcrumb>
            </header>
            <div class="p-4 pt-0">
                <slot/>
            </div>
        </SidebarInset>
    </SidebarProvider>
</template>
