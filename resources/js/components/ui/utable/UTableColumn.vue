<script setup lang="ts">
import {defineComponent, inject, onMounted, useSlots} from 'vue';
import {TableColumnProps, TableContext} from "@js/components/ui/utable/types.ts";

const props = defineProps<TableColumnProps>();
const slots = useSlots();
const table = inject<TableContext>('dataSource');

onMounted(() => {
    if (table) {
        const column: any = {
            prop: props.prop,
            label: props.label,
            headerClass: props.headerClass,
            cellClass: props.cellClass,
        };
        if (slots.header) {
            column.headerComponent = defineComponent({
                setup: () => () => slots.header!()
            });
        }
        if (slots.default) {
            column.cellComponent = defineComponent({
                props: ['row', 'index', 'provider'],
                setup: (props) => () => slots.default!({row: props.row, index: props.index, provider: props.provider})
            });
        }
        table.addColumn(column);
    }
});
</script>

<template>

</template>

<style scoped>

</style>
