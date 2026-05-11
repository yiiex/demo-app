<script setup>
import DashboardLayout from "@js/layouts/DashboardLayout.vue";
import TaskTable from "@js/pages/Task/Components/TaskTable.vue";

defineOptions({
    layout: DashboardLayout,
});
defineProps({
    dataProvider: {
        type: Object,
        required: true
    },
    actions: {
        type: Array,
        required: false,
    },
});
</script>

<template>
    <TaskTable url="/task/index" :data-provider="dataProvider" :actions="actions"/>
</template>

<style scoped>

</style>
