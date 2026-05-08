<script setup>

import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem
} from "@js/components/ui/sidebar/index.ts";
import NavUser from "@js/layouts/DashboardNavUser.vue";
import DashboardNavMain from "@js/layouts/DashboardNavMain.vue";
import {ref} from "vue";
import {BookOpen, Bot, Settings2, Sparkles, SquareTerminal} from "@lucide/vue";
import {usePage} from "@inertiajs/vue3";

const sidebarItems = ref([
    {
        title: "Workspace",
        url: "#",
        icon: SquareTerminal,
        isActive: true,
        items: [
            {
                title: "All tasks",
                url: "/task/index",
            },
            {
                title: "Projects",
                url: "/project/index",
            },
        ],
    },
    {
        title: "Users",
        url: "#",
        icon: Bot,
        items: [
            {
                title: "All users",
                url: "/user/index",
            },
        ],
    },
    {
        title: "Documentation",
        url: "/documentation",
        icon: BookOpen,
    },
]);
const page = usePage();
</script>

<template>
    <Sidebar collapsible="icon" variant="inset" >
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton
                        as-child
                        class="data-[slot=sidebar-menu-button]:!p-1.5"
                    >
                        <a href="#">
                            <Sparkles class="!size-5" />
                            <span class="text-base font-semibold">{{ page.props.appName }}</span>
                        </a>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>
        <SidebarContent>
            <DashboardNavMain :items="sidebarItems"/>
        </SidebarContent>
        <SidebarFooter>
            <NavUser/>
        </SidebarFooter>
    </Sidebar>
</template>

<style scoped>

</style>
