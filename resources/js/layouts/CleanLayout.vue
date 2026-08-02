<script setup lang="ts">
import {useTheme} from '@js/composables/useTheme'
import {Button} from '@js/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger
} from '@js/components/ui/dropdown-menu';
import {Monitor, Moon, Sun} from '@lucide/vue';
import {computed} from 'vue';
import {Head, usePage} from "@inertiajs/vue3";
import {Toaster} from "@js/components/ui/sonner";

const {currentTheme, appliedTheme, setTheme} = useTheme()

const themes = [
    {value: 'light', label: 'Light', icon: Sun},
    {value: 'dark', label: 'Dark', icon: Moon},
    {value: 'system', label: 'System', icon: Monitor},
] as const;

const currentThemeLabel = computed(() => {
    return themes.find(theme => theme.value === currentTheme.value)?.label;
});

const currentIcon = computed(() => {
    if (currentTheme.value === 'system') {
        return appliedTheme.value === 'dark' ? Moon : Sun;
    }
    return currentTheme.value === 'dark' ? Moon : Sun;
});

const page = usePage();
const pageTitle = computed(() => {
    const title = page.props.title;
    return typeof title === 'string' ? title : '';
});
</script>
<template>
    <div class="min-h-screen">
        <Head :title="pageTitle"/>
        <Toaster position="top-center" :theme="currentTheme"/>
        <slot/>
        <div class="fixed top-4 right-4 z-50">
            <DropdownMenu>
                <DropdownMenuTrigger as-child>
                    <Button variant="outline" class="gap-2 rounded-full">
                        <component :is="currentIcon" class="h-4 w-4"/>
                        <span>{{ currentThemeLabel }}</span>
                    </Button>
                </DropdownMenuTrigger>
                <DropdownMenuContent align="end">
                    <DropdownMenuItem
                        v-for="theme in themes"
                        :key="theme.value"
                        @click="setTheme(theme.value)"
                        :class="{ 'bg-accent': currentTheme === theme.value }"
                    >
                        <component :is="theme.icon" class="mr-2 h-4 w-4"/>
                        <span>{{ theme.label }}</span>
                    </DropdownMenuItem>
                </DropdownMenuContent>
            </DropdownMenu>
        </div>
    </div>
</template>
