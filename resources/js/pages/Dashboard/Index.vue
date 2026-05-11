<script setup>

import DashboardLayout from "@js/layouts/DashboardLayout.vue";
import TaskTable from "@js/pages/Task/Components/TaskTable.vue";
import DashboardCards from "@js/pages/Dashboard/Components/DashboardCards.vue";
import {useAuth} from "@js/composables/useAuth.ts";
import {router} from "@inertiajs/vue3";

defineOptions({
    layout: DashboardLayout,
});
defineProps({
    taskSummary: {
        type: Object,
        required: true,
    },
    problemTaskSummary: {
        type: Object,
        required: true,
    },
    timeSummary: {
        type: Object,
        required: true,
    },
});
const {user} = useAuth();

const handleRefresh = (card, onComplete) => {
    router.reload({
        only: [card],
        onFinish: () => onComplete(),
    })
}
</script>

<template>
    <div class="flex flex-1 flex-col gap-4">
        <DashboardCards :taskSummary="taskSummary"
                        :timeSummary="timeSummary"
                        :problemTaskSummary="problemTaskSummary"
                        @refresh="handleRefresh"/>
        <div class="flex-1 rounded-xl">
            <div class="mt-4 mb-2">
                <h3 class="text-1xl font-bold tracking-tight">
                    Hello, {{ user.fullName }} 👋
                </h3>
                <p class="text-sm text-muted-foreground mt-1">
                    What you're working on
                </p>
            </div>
            <TaskTable url="/dashboard/tasks" mode="lazy"/>
        </div>
    </div>
</template>

<style scoped>

</style>
