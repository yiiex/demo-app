<script setup lang="ts">
import {computed, inject, ref} from 'vue';
import {router} from '@inertiajs/vue3';
import {Method} from '@inertiajs/core';
import {Button} from '@/js/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger
} from '@/js/components/ui/dropdown-menu';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle
} from '@/js/components/ui/dialog';
import {MoreHorizontal} from '@lucide/vue';
import {Action} from "@js/components/ui/uaction/types.ts";
import {toast} from "vue-sonner";
import axios from "axios";
import {DataSourceContext} from "@js/components/ui/udata/types.ts";

const props = defineProps<{
    actions: Action[];
    mode?: 'default' | 'compact';
}>();

const visibleActions = computed(() =>
    props.actions.filter(action => action.visible !== false)
);
const dataSource = inject<DataSourceContext | undefined>('dataSource', undefined);

const execute = (action: Action) => {
    if (action.confirm) {
        confirmDialog.value = {
            open: true,
            title: 'Confirmation',
            description: action.confirm,
            action: action,
        };
    } else {
        performAction(action);
    }
};

const performAction = (action: Action) => {
    const method = (action.method?.toLowerCase() || 'get') as Method;

    if (action.async) {
        const requestPromise = axios({
            method: method,
            url: action.url,
        });

        toast.promise(requestPromise, {
            loading: action.process || 'Processing...',
            success: (response: any) => {
                return response?.data?.message || 'Request completed successfully';
            },
            error: (error: any) => {
                return error?.response?.data?.message || 'Request error';
            }
        });
        requestPromise
            .then((response: any) => {
                if (response?.data?.redirectUrl) {
                    router.visit(response.data.redirectUrl);
                } else if (dataSource) {
                    dataSource.refresh();
                }
            })
            .catch((error: any) => {
                console.error('Request failed:', error);
            });
    } else {
        router.visit(action.url, {method});
    }
};

const confirmDialog = ref<{
    open: boolean;
    title: string;
    description: string;
    action: Action | null;
}>({
    open: false,
    title: 'Confirmation',
    description: 'Are you sure you want to confirm this action?',
    action: null,
});

const handleConfirmCancel = () => {
    confirmDialog.value.open = false;
}
const handleConfirm = () => {
    if (confirmDialog.value.action) {
        performAction(confirmDialog.value.action);
        confirmDialog.value.open = false;
    }
}

const handleDialogOpenChange = (open: boolean) => {
    confirmDialog.value.open = open;
};

const hasActions = computed(() => {
    return (props.actions || []).length > 0;
})
</script>

<template>
    <Dialog :open="confirmDialog.open" @update:open="handleDialogOpenChange">
        <DialogContent>
            <DialogHeader>
                <DialogTitle>{{ confirmDialog.title }}</DialogTitle>
                <DialogDescription>
                    {{ confirmDialog.description }}
                </DialogDescription>
            </DialogHeader>
            <DialogFooter>
                <Button variant="outline" @click="handleConfirmCancel">Cancel</Button>
                <Button variant="destructive" @click="handleConfirm">Confirm</Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
    <template v-if="hasActions">
        <DropdownMenu v-if="mode === 'compact'">
            <DropdownMenuTrigger>
                <Button variant="ghost" size="sm">
                    <MoreHorizontal class="w-4 h-4"/>
                </Button>
            </DropdownMenuTrigger>
            <DropdownMenuContent align="end">
                <DropdownMenuItem v-for="action in visibleActions" :key="action.title" @click="execute(action)"
                                  class="cursor-pointer"
                                  :variant="(action.variant as 'default' | 'destructive' | undefined)">
                    <component :is="action.icon" v-if="action.icon" class="w-4 h-4 mr-2"/>
                    {{ action.title }}
                </DropdownMenuItem>
            </DropdownMenuContent>
        </DropdownMenu>
        <div v-else class="flex gap-2">
            <Button
                v-for="action in visibleActions"
                :key="action.title"
                :variant="action.variant || 'outline'"
                size="sm"
                @click="execute(action)"
            >
                <component :is="action.icon" v-if="action.icon" class="w-4 h-4 mr-1"/>
                {{ action.title }}
            </Button>
        </div>
    </template>
</template>

<style scoped>

</style>
