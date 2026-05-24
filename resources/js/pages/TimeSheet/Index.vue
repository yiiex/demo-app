<script setup>
import DashboardLayout from "@js/layouts/DashboardLayout.vue";
import {Calendar, CalendarCellTrigger} from "@js/components/ui/calendar/index.ts";
import {parseDate} from '@internationalized/date';
import {ref, watch} from 'vue';
import {router} from '@inertiajs/vue3';
import {UTable, UTableColumn} from "@js/components/ui/utable/index.ts";
import {UActionLink, UActionList} from "@js/components/ui/uaction/index.ts";

defineOptions({
    layout: DashboardLayout,
});

const props = defineProps({
    timeEntries: {
        type: Array,
    },
    taskDataProvider: {
        type: Object,
    },
    date: {
        type: String,
    },
    currentUrl: {
        type: String,
    },
});

const date = ref(parseDate(props.date));
const placeholder = ref(parseDate(props.date));

const getDuration = (day) => {
    return props.timeEntries.find(t => t.date == day.toString())?.duration ?? '0h';
};

const hasTimeEntry = (day) => {
    return props.timeEntries.some(t => t.date == day.toString());
};

watch(placeholder, (newVal, oldVal) => {
    if (newVal.month !== oldVal.month || newVal.year !== oldVal.year) {
        date.value = newVal;
    }
});

watch(date, (newVal, oldVal) => {
    if (newVal && newVal.toString() !== oldVal?.toString()) {
        router.get(props.currentUrl, {
            date: newVal.toString(),
        }, {
            preserveState: true,
            preserveScroll: true,
        });
    }
});
</script>

<template>
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-4">
        <Calendar
            v-model="date"
            v-model:placeholder="placeholder"
            class="rounded-md border shadow-sm table"
            layout="month-and-year"
        >
            <template #day="{ day, month }">
                <CalendarCellTrigger
                    :day="day"
                    :month="month"
                    class="h-14 w-full cursor-pointer"
                >
                    <div class="flex items-center justify-center h-full w-full"
                         :class="hasTimeEntry(day) ? 'text-green-600 dark:text-green-500' : ''"
                    >
                        <div class="text-sm font-semibold">{{ getDuration(day) }}</div>
                    </div>
                    <div class="absolute top-1 right-1.5 text-[10px] font-medium text-muted-foreground leading-none">
                        {{ day.day }}
                    </div>
                </CalendarCellTrigger>
            </template>
        </Calendar>
        <div class="">
            <UTable :data="taskDataProvider" :url="currentUrl">
                <UTableColumn prop="reference" label="ID"/>
                <UTableColumn prop="shortName" label="Name" cellClass="max-w-[200px] whitespace-normal">
                    <template #default="{ row, index, provider }">
                        <UActionLink :actions="provider.actions[index] || []"
                                     actionName="show">{{ row.shortName }}
                        </UActionLink>
                    </template>
                </UTableColumn>
                <UTableColumn label="Actions">
                    <template #default="{ row, index, provider }">
                        <UActionList :actions="provider.actions[index]" mode="compact"/>
                    </template>
                </UTableColumn>
            </UTable>
        </div>
    </div>
</template>

<style scoped>
</style>
