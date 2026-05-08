<script setup lang="ts">
import {computed, onMounted, provide, shallowRef, useSlots} from "vue";
import {Table, TableBody, TableCell, TableEmpty, TableHead, TableHeader, TableRow} from "@js/components/ui/table";
import {Column, TableContext, TableProps} from "@js/components/ui/utable/types.ts";
import {UForm} from "@js/components/ui/uform/index";
import UDataPagination from "@js/components/ui/udata/UDataPagination.vue";
import {useDataProvider} from "@js/composables/useDataProvider.ts";
import {Spinner} from "@js/components/ui/spinner";
import {CrudDataProvider} from "@js/components/ui/udata/types.ts";

const slots = useSlots();
const props = withDefaults(defineProps<TableProps>(), {
    mode: 'default',
});
const emit = defineEmits<{
    'update:data': [data: CrudDataProvider]
}>();

const {
    dataProvider,
    hasData,
    loading,
    filterModel,
    fetch,
} = useDataProvider(props, emit);

const columns = shallowRef<Column[]>([]);
const addColumn = (column: Column) => {
    if (!columns.value.some(col => col.prop === column.prop)) {
        columns.value = [...columns.value, column];
    }
};

onMounted(() => {
    if (props.mode === 'lazy' && !props.data) {
        fetch();
    }
});

provide<TableContext>('dataSource', {
    addColumn: addColumn,
    refresh: fetch,
});

const colspan = computed(() => columns.value.length);
const hasFilterSlot = computed(() => !!(slots.filter || slots.buttons));

const handlePageChange = (page: number) => {
    fetch(page);
};
</script>
<template>
    <div
        class="sm:rounded-lg relative overflow-hidden outline outline-offset-2 outline-neutral-200 dark:outline-neutral-900">
        <div v-if="loading"
             class="absolute inset-0 bg-background/50 flex items-center justify-center z-10 backdrop-blur-xs">
            <Spinner class="size-8"/>
        </div>
        <div v-if="hasFilterSlot" class="p-3">
            <div class="flex justify-between">
                <div class="" v-if="url">
                    <UForm :url="url" :model="filterModel" :attribute-labels="dataProvider?.filter?.attributeLabels"
                           :safe-attributes="dataProvider?.filter?.safeAttributes">
                        <slot name="filter" :model="filterModel"/>
                    </UForm>
                </div>
                <slot name="buttons"/>
            </div>
        </div>
        <Table>
            <TableHeader class="bg-muted">
                <TableRow>
                    <TableHead v-for="column in columns" :key="column.prop" :class="column.headerClass">
                        <component
                            v-if="column.headerComponent"
                            :is="column.headerComponent"
                        />
                        <template v-else>{{ column.label }}</template>
                    </TableHead>
                </TableRow>
            </TableHeader>
            <TableBody>
                <TableRow v-if="hasData" v-for="(row, index) in dataProvider?.data" :key="index">
                    <TableCell v-for="column in columns" :key="column.prop" :class="column.cellClass">
                        <component
                            v-if="column.cellComponent"
                            :is="column.cellComponent"
                            :row="row"
                            :index="index"
                            :provider="dataProvider"
                        />
                        <template v-else>{{ row[column.prop] ?? '' }}</template>
                    </TableCell>
                </TableRow>
                <TableEmpty v-else :colspan="colspan">
                    No data available
                </TableEmpty>
            </TableBody>
        </Table>
        <slot/>
        <div class="flex flex-col gap-6 p-2 bg-muted" v-if="dataProvider?.meta">
            <UDataPagination :meta="dataProvider.meta" @update:page="handlePageChange"/>
        </div>
    </div>
</template>

<style scoped>

</style>
