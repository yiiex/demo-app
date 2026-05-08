<script setup lang="ts">
import {
    Pagination,
    PaginationContent,
    PaginationItem,
    PaginationPrevious,
    PaginationNext,
    PaginationEllipsis
} from "@js/components/ui/pagination";
import {PaginationProps} from "@js/components/ui/udata/types.ts";

const emit = defineEmits<{
    (e: 'update:page', page: number): void;
}>();

defineProps<PaginationProps>();
const handlePageChange = (page: number) => {
    emit('update:page', page);
};
</script>

<template>
    <div class="flex flex-col gap-4">
        <Pagination v-slot="{ page }" v-model:page="meta.page" :items-per-page="meta.perPage" :total="meta.total"
                    :default-page="1"
                    @update:page="handlePageChange" class="justify-end">
            <PaginationContent v-slot="{ items }">
                <PaginationPrevious class="cursor-pointer"/>
                <template v-for="(item, index) in items" :key="index">
                    <PaginationItem
                        class="cursor-pointer"
                        v-if="item.type === 'page'"
                        :value="item.value"
                        :is-active="item.value === page"
                    >
                        {{ item.value }}
                    </PaginationItem>
                    <PaginationEllipsis v-else :key="item.type" :index="index"/>
                </template>
                <PaginationNext class="cursor-pointer"/>
            </PaginationContent>
        </Pagination>
    </div>
</template>

<style scoped>

</style>
