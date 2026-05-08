<script setup>
import DashboardLayout from "@js/layouts/DashboardLayout.vue";
import CommentForm from "@js/pages/TaskComment/Components/CommentForm.vue";
import {Card, CardContent} from "@js/components/ui/card/index.ts";

defineOptions({
    layout: DashboardLayout,
});
const props = defineProps({
    form: null,
    saveUrl: null,
});
</script>

<template>
    <Card>
        <CardContent>
            <CommentForm :form="form" :saveUrl="saveUrl"/>
        </CardContent>
    </Card>
</template>
