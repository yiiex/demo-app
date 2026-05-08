<script setup>

import DashboardLayout from "@js/layouts/DashboardLayout.vue";

defineOptions({
    layout: DashboardLayout,
});
</script>

<template>
    <div class="flex flex-1 flex-col gap-4">
        <div class="grid auto-rows-min gap-4 md:grid-cols-3">
            <div class="bg-muted/50 aspect-video rounded-xl" />
            <div class="bg-muted/50 aspect-video rounded-xl" />
            <div class="bg-muted/50 aspect-video rounded-xl" />
        </div>
        <div class="bg-muted/50 min-h-96 flex-1 rounded-xl" />
    </div>
</template>

<style scoped>

</style>
