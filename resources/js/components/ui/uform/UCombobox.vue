<script setup lang="ts">
import {computed, onMounted, ref, watch} from 'vue'
import {debounce} from 'lodash'
import axios from 'axios'
import {Popover, PopoverContent, PopoverTrigger} from "@js/components/ui/popover";
import {Button} from "@js/components/ui/button";
import {CheckIcon, ChevronsUpDownIcon, XIcon} from '@lucide/vue'
import {Command, CommandEmpty, CommandGroup, CommandInput, CommandList, CommandItem} from "@js/components/ui/command";
import {SelectListItem} from "@js/components/ui/uform/types.ts";
import {toast} from "vue-sonner";
import {Badge} from "@js/components/ui/badge";

const props = withDefaults(defineProps<{
    providerUrl?: string,
    provider?: string | null,
    context?: Record<string, any>,
    multiple?: boolean,
}>(), {
    provider: null,
    providerUrl: '/autocompleteProvider',
    multiple: false,
});

const modelValue = defineModel<string | number | null | (string | number)[]>();
const open = ref(false);
const search = ref('');
const items = ref<Array<SelectListItem>>([]);
const loading = ref(false);

onMounted(() => {
    if (modelValue.value) {
        loadItems();
    }
});

const loadItems = debounce(async () => {
    loading.value = true
    await axios.post(props.providerUrl, {
        ...props.context,
        search: search.value || null,
        value: modelValue.value,
        provider: props.provider,
    }).then(result => {
        if (result.data?.data) {
            const newItems = result.data.data as SelectListItem[];
            const missingSelectedItems = selectedItems.value.filter(
                selectedItem => !newItems.some(item => item.key === selectedItem.key)
            );
            items.value = [...missingSelectedItems, ...newItems];
        }
    }).catch(error => {
        if (error.response.data) {
            toast.error(error.response.data.message);
        }
    }).finally(() => {
        loading.value = false
    })
}, 300);

watch(open, (isOpen) => {
    if (isOpen && !items.value.length) loadItems()
})

watch(search, () => loadItems());

const handleSelect = (item: SelectListItem) => {
    if (props.multiple) {
        const currentValues = Array.isArray(modelValue.value) ? modelValue.value : [];
        const isSelected = currentValues.includes(item.key);
        let newValues: (string | number)[];
        if (isSelected) {
            newValues = currentValues.filter(value => value !== item.key);
        } else {
            newValues = [...currentValues, item.key];
        }
        modelValue.value = newValues.length === 0 ? null : newValues;
    } else {
        modelValue.value = modelValue.value === item.key ? null : item.key;
        open.value = false;
        search.value = '';
    }
};

const removeItem = (item: SelectListItem) => {
    if (props.multiple) {
        const currentValues = Array.isArray(modelValue.value) ? modelValue.value : [];
        modelValue.value = currentValues.filter(value => value !== item.key);
        if (modelValue.value.length === 0) {
            modelValue.value = null;
        }
    } else {
        modelValue.value = null;
    }
};

const selectedItems = computed(() => {
    const selectedKeys = Array.isArray(modelValue.value)
        ? modelValue.value
        : (modelValue.value !== null && modelValue.value !== undefined ? [modelValue.value] : []);
    return items.value.filter(item => selectedKeys.includes(item.key));
});
</script>

<template>
    <Popover v-model:open="open">
        <PopoverTrigger as-child>
            <Button variant="outline" class="w-full flex justify-start min-h-9 h-auto">
                <div class="flex gap-2 flex-wrap whitespace-normal">
                    <Badge v-if="selectedItems.length" v-for="item in selectedItems" :key="item.key">
                        {{ item.label }}
                        <span @click.stop="removeItem(item)" class="cursor-pointer hover:scale-125 transition-transform">
                            <XIcon class="h-3 w-3 hover:text-destructive"/>
                        </span>
                    </Badge>
                    <span v-else>Select...</span>
                </div>
                <ChevronsUpDownIcon class="my-auto ml-auto h-4 w-4 shrink-0"/>
            </Button>
        </PopoverTrigger>
        <PopoverContent class="p-0" align="start">
            <Command>
                <CommandInput v-model="search" placeholder="Search..."/>
                <CommandList>
                    <CommandEmpty>No results.</CommandEmpty>
                    <CommandGroup>
                        <CommandItem
                            v-for="item in items"
                            :key="item.key"
                            :value="item.label"
                            @select="handleSelect(item)"
                            class="hover:bg-accent hover:text-accent-foreground cursor-pointer">
                            <CheckIcon
                                :class="selectedItems.some(i => i.key === item.key) ? 'opacity-100' : 'opacity-0'"/>
                            {{ item.label }}
                        </CommandItem>
                    </CommandGroup>
                </CommandList>
            </Command>
        </PopoverContent>
    </Popover>
</template>
