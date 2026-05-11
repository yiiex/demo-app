// DashboardCards.vue
<script setup lang="ts">
import {AlertCircle, Clock, ListTodo, RefreshCw} from 'lucide-vue-next'
import {Button} from '@js/components/ui/button'
import {Spinner} from "@js/components/ui/spinner";
import {useDelayedLoading} from "@js/composables/useDelayedRef.ts";
import {toast} from "vue-sonner";

interface TaskSummary {
    total: number,
    completed: number,
    inProgress: number,
}

interface ProblemTaskSummary {
    overdue: number,
}

interface TimeSummary {
    total: string,
    today: string,
    week: string,
}

defineProps<{
    taskSummary: TaskSummary,
    problemTaskSummary: ProblemTaskSummary,
    timeSummary: TimeSummary,
}>();

const emit = defineEmits<{
    refresh: [card: 'taskSummary' | 'timeSummary' | 'problemTaskSummary', onComplete: () => void]
}>()

const { loadings, setLoading } = useDelayedLoading([
    'taskSummary',
    'timeSummary',
    'problemTaskSummary',
], 150);

const handleRefresh = (card: 'taskSummary' | 'timeSummary' | 'problemTaskSummary') => {
    setLoading(card, true);
    emit('refresh', card, () => {
        setLoading(card, false);
        toast.success('Data updated successfully');
    })
}
</script>

<template>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

        <!-- Tasks -->
        <div
            class="relative group flex items-center justify-between p-4 rounded-lg bg-card outline outline-offset-2 outline-neutral-200 dark:outline-neutral-800 transition-all duration-700 hover:outline-blue-300 dark:hover:outline-blue-500 hover:shadow-lg hover:shadow-blue-500/5">
            <div v-if="loadings.taskSummary.value" class="absolute inset-0 bg-background/50 flex items-center justify-center z-10 backdrop-blur-xs rounded-lg">
                <Spinner class="size-6"/>
            </div>
            <div class="flex items-center gap-3 min-w-0">
                <div
                    class="p-2 rounded-md bg-blue-50 dark:bg-blue-950 shrink-0 transition-all duration-300 group-hover:scale-110">
                    <ListTodo class="size-4 text-blue-500 transition-all duration-300 group-hover:rotate-12"/>
                </div>
                <div class="min-w-0">
                    <div class="text-xl font-bold transition-all duration-300 group-hover:text-blue-500">
                        All tasks: {{ taskSummary.total }}
                    </div>
                    <div class="text-xs text-muted-foreground truncate">
                        {{ taskSummary.inProgress }} in progress · {{ taskSummary.completed }} done
                    </div>
                </div>
            </div>
            <Button variant="ghost" size="icon"
                    class="shrink-0 opacity-0 transition-all duration-300 group-hover:opacity-100"
                    @click="handleRefresh('taskSummary')">
                <RefreshCw class="size-4"/>
            </Button>
        </div>

        <!-- Hours -->
        <div
            class="relative group flex items-center justify-between p-4 rounded-lg bg-card outline outline-offset-2 outline-neutral-200 dark:outline-neutral-800 transition-all duration-700 hover:outline-green-300 dark:hover:outline-green-600 hover:shadow-lg hover:shadow-green-500/5">
            <div v-if="loadings.timeSummary.value" class="absolute inset-0 bg-background/50 flex items-center justify-center z-10 backdrop-blur-xs rounded-lg">
                <Spinner class="size-6"/>
            </div>
            <div class="flex items-center gap-3 min-w-0">
                <div
                    class="p-2 rounded-md bg-green-50 dark:bg-green-950 shrink-0 transition-all duration-300 group-hover:scale-110">
                    <Clock class="size-4 text-green-600 transition-all duration-300 group-hover:rotate-12"/>
                </div>
                <div class="min-w-0">
                    <div class="text-xl font-bold transition-all duration-300 group-hover:text-green-600">
                        Today: {{ timeSummary.today }}
                    </div>
                    <div class="text-xs text-muted-foreground truncate">
                        {{ timeSummary.week }} this week · {{ timeSummary.total }} total
                    </div>
                </div>
            </div>
            <Button variant="ghost" size="icon"
                    class="shrink-0 opacity-0 transition-all duration-300 group-hover:opacity-100"
                    @click="handleRefresh('timeSummary')">
                <RefreshCw class="size-4"/>
            </Button>
        </div>

        <!-- Overdue -->
        <div
            class="relative group flex items-center justify-between p-4 rounded-lg bg-card outline-1 outline-offset-2 transition-all duration-700"
            :class="problemTaskSummary.overdue
                ? 'outline-red-200 dark:outline-red-800 hover:outline-red-300 dark:hover:outline-red-500 hover:shadow-lg hover:shadow-red-500/5'
                : 'outline-neutral-200 dark:outline-neutral-800 hover:outline-neutral-300 dark:hover:outline-neutral-600 hover:shadow-lg hover:shadow-gray-500/5'"
        >
            <div v-if="loadings.problemTaskSummary.value" class="absolute inset-0 bg-background/50 flex items-center justify-center z-10 backdrop-blur-xs rounded-lg">
                <Spinner class="size-6"/>
            </div>
            <div class="flex items-center gap-3 min-w-0">
                <div
                    class="p-2 rounded-md shrink-0 transition-all duration-300 group-hover:scale-110"
                    :class="problemTaskSummary.overdue ? 'bg-red-50 dark:bg-red-950' : 'bg-gray-50 dark:bg-gray-800'"
                >
                    <AlertCircle
                        class="size-4 transition-all duration-300 group-hover:rotate-12"
                        :class="problemTaskSummary.overdue ? 'text-red-600 animate-pulse' : 'text-gray-400'"
                    />
                </div>
                <div class="min-w-0">
                    <div
                        class="text-xl font-bold transition-all duration-300"
                        :class="problemTaskSummary.overdue ? 'text-red-600 group-hover:text-red-500 animate-pulse' : 'group-hover:text-gray-900 dark:group-hover:text-gray-100'"
                    >
                        Overdue: {{ problemTaskSummary.overdue }}
                    </div>
                    <div class="text-xs text-muted-foreground truncate">
                        {{ problemTaskSummary.overdue ? 'Needs attention' : 'All clear' }}
                    </div>
                </div>
            </div>
            <Button variant="ghost" size="icon"
                    class="shrink-0 opacity-0 transition-all duration-300 group-hover:opacity-100"
                    @click="handleRefresh('problemTaskSummary')">
                <RefreshCw class="size-4"/>
            </Button>
        </div>

    </div>
</template>
