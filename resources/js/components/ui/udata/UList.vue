<script setup lang="ts">
import {useDataProvider} from "@js/composables/useDataProvider.ts";
import {CrudDataProvider, DataListProps, DataSourceContext} from "@js/components/ui/udata/types.ts";
import {onMounted, provide} from "vue";
import UDataPagination from "@js/components/ui/udata/UDataPagination.vue";
import {Spinner} from "@js/components/ui/spinner";

const props = withDefaults(defineProps<DataListProps>(), {
    mode: 'default',
});
const emit = defineEmits<{
    'update:data': [data: CrudDataProvider]
}>();
const {dataProvider, fetch, loading, hasData} = useDataProvider(props, emit);

onMounted(() => {
    if (props.mode === 'lazy' && !props.data) {
        fetch();
    }
});

provide<DataSourceContext>('dataSource', {
    refresh: fetch,
});

const handlePageChange = (page: number) => {
    fetch(page);
};
</script>

<template>
    <div>
        <slot name="prepend"/>
        <div class="relative" :class="containerClass">
            <div v-if="loading"
                 class="absolute inset-0 bg-background/50 flex items-center justify-center z-10 backdrop-blur-xs">
                <Spinner class="size-8"/>
            </div>
            <div v-if="hasData" :class="listClass">
                <template v-for="(item, index) in dataProvider?.data" :key="item.id || index">
                    <slot name="default" :index="index" :item="item" :provider="dataProvider"/>
                </template>
            </div>
            <div v-else-if="!loading" class="py-2 text-muted-foreground">
                <slot name="empty">
                    No data available
                </slot>
            </div>
            <div class="flex flex-col gap-6 p-2 bg-muted" v-if="hasData && dataProvider?.meta" :class="paginationWrapperClass">
                <UDataPagination :meta="dataProvider.meta" @update:page="handlePageChange" :class="paginationClass"/>
            </div>
        </div>
        <slot name="append"/>
    </div>
</template>
