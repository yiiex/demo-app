import {CrudDataProvider, DataSourceProps} from "@js/components/ui/udata/types.ts";
import {computed, nextTick, onUnmounted, ref, watch} from "vue";
import {debounce, pickBy} from "lodash";
import {router} from "@inertiajs/vue3";
import axios from "axios";
import {useDelayedRef} from "@js/composables/useDelayedRef.ts";

export function useDataProvider(
    props: DataSourceProps,
    emit?: {
        (event: 'update:data', data: CrudDataProvider): void
    }
) {
    const loading = useDelayedRef(false, 130);
    const lazyData = ref<CrudDataProvider | undefined>();

    const dataProvider = computed<CrudDataProvider | undefined>(() => {
        return props.mode === 'lazy' ? lazyData.value : props.data;
    });

    let isInternalUpdate = false;
    const filterModel = ref({...dataProvider.value?.filter?.model});
    const hasData = computed(() => dataProvider.value?.meta && dataProvider.value?.meta?.count > 0);

    const fetch = async (page?: number) => {
        if (props.url === undefined) {
            return;
        }
        const cleanFilters = pickBy(filterModel.value || {}, (value) => {
            return value !== null && value !== undefined && value !== '';
        });

        const params = {
            page: page ?? 1,
            ...cleanFilters
        };

        loading.value = true;
        if (props.mode === 'lazy') {
            try {
                const response = await axios.get(props.url, {params});
                lazyData.value = response.data.data;
                emit && emit('update:data', response.data.data);
            } finally {
                loading.value = false;
            }
        } else {
            router.get(props.url, params, {
                preserveState: true,
                preserveScroll: true,
                replace: true,
                onFinish: () => {
                    loading.value = false
                },
                onError: () => {
                    loading.value = false
                }
            });
        }
    };

    const debouncedFetch = debounce(() => {
        fetch();
    }, 600);

    watch(filterModel, () => {
        if (isInternalUpdate) {
            return;
        }
        debouncedFetch();
    }, {deep: true});

    watch(() => props.data?.filter?.model, (newModel) => {
        if (!newModel) return;
        isInternalUpdate = true;
        Object.assign(filterModel.value, newModel);
        nextTick(() => isInternalUpdate = false);
    }, {deep: true});

    onUnmounted(() => {
        debouncedFetch.cancel();
    });

    return {
        dataProvider,
        hasData,
        loading,
        filterModel,
        fetch,
        debouncedFetch,
    }
}
