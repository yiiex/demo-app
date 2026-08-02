<script setup lang="ts">
import {
    BadgeCheck,
    Bell,
    Check,
    ChevronsUpDown,
    LogOut,
    Monitor,
    Moon,
    Palette,
    Sun,
} from "@lucide/vue";
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuGroup,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuSub,
    DropdownMenuSubContent,
    DropdownMenuSubTrigger,
    DropdownMenuTrigger,
} from "@/js/components/ui/dropdown-menu";
import {SidebarMenu, SidebarMenuButton, SidebarMenuItem, useSidebar,} from "@/js/components/ui/sidebar";
import {useAuth} from "@js/composables/useAuth.ts";
import {useTheme} from "@js/composables/useTheme.ts";
import UserAvatar from "@js/components/user/UserAvatar.vue";

const {isMobile} = useSidebar();
const {user, logoutDialog} = useAuth();
const {currentTheme, setTheme} = useTheme();

</script>

<template>
    <SidebarMenu>
        <SidebarMenuItem>
            <DropdownMenu>
                <DropdownMenuTrigger as-child>
                    <SidebarMenuButton
                        size="lg"
                        class="data-[state=open]:bg-sidebar-accent data-[state=open]:text-sidebar-accent-foreground"
                    >
                        <UserAvatar :user="user" />
                        <div class="grid flex-1 text-left text-sm leading-tight">
                            <span class="truncate font-medium">{{ user?.fullName }}</span>
                            <span class="truncate text-xs">{{ user?.email }}</span>
                        </div>
                        <ChevronsUpDown class="ml-auto size-4"/>
                    </SidebarMenuButton>
                </DropdownMenuTrigger>
                <DropdownMenuContent
                    class="w-(--reka-dropdown-menu-trigger-width) min-w-56 rounded-lg"
                    :side="isMobile ? 'bottom' : 'right'"
                    align="end"
                    :side-offset="4"
                >
                    <DropdownMenuLabel class="p-0 font-normal">
                        <div class="flex items-center gap-2 px-1 py-1.5 text-left text-sm">
                            <UserAvatar :user="user" />
                            <div class="grid flex-1 text-left text-sm leading-tight">
                                <span class="truncate font-semibold">{{ user?.fullName }}</span>
                                <span class="truncate text-xs">{{ user?.email }}</span>
                            </div>
                        </div>
                    </DropdownMenuLabel>
                    <DropdownMenuSeparator/>
                    <DropdownMenuSub>
                        <DropdownMenuSubTrigger>
                            <Palette class="size-4"/>
                            <span>Appearance</span>
                        </DropdownMenuSubTrigger>
                        <DropdownMenuSubContent class="min-w-40">
                            <DropdownMenuItem @select.prevent="setTheme('light')">
                                <Sun class="size-4"/>
                                <span>Light</span>
                                <Check v-if="currentTheme === 'light'" class="ml-auto size-4"/>
                            </DropdownMenuItem>
                            <DropdownMenuItem @select.prevent="setTheme('dark')">
                                <Moon class="size-4"/>
                                <span>Dark</span>
                                <Check v-if="currentTheme === 'dark'" class="ml-auto size-4"/>
                            </DropdownMenuItem>
                            <DropdownMenuSeparator/>
                            <DropdownMenuItem @select.prevent="setTheme('system')">
                                <Monitor class="size-4"/>
                                <span>System</span>
                                <Check v-if="currentTheme === 'system'" class="ml-auto size-4"/>
                            </DropdownMenuItem>
                        </DropdownMenuSubContent>
                    </DropdownMenuSub>
                    <DropdownMenuSeparator/>
                    <DropdownMenuGroup>
                        <DropdownMenuItem @click="$inertia.visit('/user/show/' + user?.id)">
                            <BadgeCheck/>
                            Account
                        </DropdownMenuItem>
                        <DropdownMenuItem>
                            <Bell/>
                            Notifications
                        </DropdownMenuItem>
                    </DropdownMenuGroup>
                    <DropdownMenuSeparator/>
                    <DropdownMenuItem @select="logoutDialog = true">
                        <LogOut/>
                        Log out
                    </DropdownMenuItem>
                </DropdownMenuContent>
            </DropdownMenu>
        </SidebarMenuItem>
    </SidebarMenu>
</template>

<style scoped>

</style>
