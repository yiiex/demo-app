<!-- DebugPanel.vue -->
<script setup lang="ts">
import {router} from '@inertiajs/vue3';
import {computed, onMounted, onUnmounted, ref} from 'vue';
import {Button} from '@js/components/ui/button/index.ts';
import {
    Sheet, SheetClose,
    SheetContent,
    SheetDescription,
    SheetHeader,
    SheetTitle,
    SheetTrigger,
} from '@js/components/ui/sheet/index.ts';
import {ScrollArea} from '@js/components/ui/scroll-area/index.ts';
import {Bug, Clock, Database, Cpu, X, Maximize2, Minimize2} from 'lucide-vue-next';
import {formatBytes, formatMs} from "@js/components/ui/debug";
import axios from "axios";
import {DebugDetail} from "@js/components/ui/debug";
import {Badge} from "@js/components/ui/badge";

const debugList = ref<DebugLog[]>([]);
const debugIds = ref<string[]>([]);
const selectedId = ref<string | null>(null);
const isFullscreen = ref<boolean>(false);

const toggleFullscreen = (): void => {
    isFullscreen.value = !isFullscreen.value;
};

const selectedDebug = computed<DebugLog | undefined>(() =>
    debugList.value.find(deb => deb.id === selectedId.value)
);

const lastDebug = computed<DebugLog | null>(() => debugList.value[0] || null);

onMounted(() => {
    // inertia
    onUnmounted(
        router.on('navigate', (event: CustomEvent) => {
            const debugId = event.detail.page?.props?.debugId as string | undefined;
            if (debugId) processDebugResponse(debugId);
        })
    );
    // axios
    const interceptorId = axios.interceptors.response.use(
        (response) => {
            const debugId = response.headers['x-debug-id'] as string | undefined;
            if (debugId) processDebugResponse(debugId);
            return response;
        },
        (error) => {
            const debugId = error.response?.headers?.['x-debug-id'] as string | undefined;
            if (debugId) processDebugResponse(debugId);
            return Promise.reject(error);
        }
    );
    onUnmounted(() => {
        axios.interceptors.response.eject(interceptorId);
    });
});

const processDebugResponse = (debugId: string): void => {
    if (!debugIds.value.includes(debugId)) {
        debugIds.value.push(debugId);
        axios.get<{ debug: DebugLog }>('/debug/show/' + debugId).then(response => {
            if (response.data.debug?.id) {
                debugList.value.unshift(response.data.debug);
                if (!selectedId.value) {
                    selectedId.value = response.data.debug.id;
                }
            }
        });
    }
};

const selectDebug = (id: string): void => {
    selectedId.value = id;
};
</script>

<template>
    <Sheet v-if="debugList.length > 0">
        <SheetTrigger as-child>
            <div
                class="debug-trigger fixed bottom-4 right-4 z-50 flex items-center gap-0
               bg-background/95 backdrop-blur rounded-full shadow-lg
               hover:shadow-xl transition-all cursor-pointer overflow-hidden
               group
               border border-muted-foreground/20
               outline-0 hover:outline-1 hover:outline-orange-500/70 dark:hover:outline-yellow-400/50
               ring-0 hover:ring-2 hover:ring-orange-600/40 dark:hover:ring-yellow-400/20
               hover:ring-offset-2 hover:ring-offset-transparent"
            >
                <div
                    class="debug-icon-wrapper relative pl-3 pr-2 py-2 flex items-center
                   transition-all duration-300 ease-out
                   group-hover:text-orange-600 dark:group-hover:text-yellow-400
                   group-hover:drop-shadow-[0_0_8px_rgba(16,185,129,0.5)]
                   dark:group-hover:drop-shadow-[0_0_8px_rgba(250,204,21,0.5)]"
                >
                    <Bug class="size-4 transition-transform duration-300 group-hover:scale-110"/>
                </div>

                <div v-if="lastDebug" class="flex items-center gap-3 py-2 px-3 text-xs font-mono border-l">
                    <span v-if="lastDebug.app?.memoryUsage" class="flex items-center gap-1 text-muted-foreground">
                        <Cpu class="size-3 m-auto"/>
                        {{ formatBytes(lastDebug.app.memoryUsage) }}
                    </span>
                    <span v-if="lastDebug.app?.requestProcessingTime"
                          class="flex items-center gap-1 text-muted-foreground">
                <Clock class="size-3"/>
                {{ formatMs(lastDebug.app.requestProcessingTime) }}
            </span>
                    <span v-if="lastDebug.queries?.length" class="flex items-center gap-1 text-muted-foreground">
                <Database class="size-3"/>
                {{ lastDebug.queries.length }}
            </span>
                </div>
            </div>
        </SheetTrigger>
        <SheetContent side="bottom" :class="[
                'p-0 transition-all duration-300', isFullscreen ? 'h-[100vh] !max-h-[100vh]' : 'h-[50vh]']">
            <div class="flex h-full relative">
                <div class="absolute top-3 right-3 z-20 flex items-center gap-2">
                    <Button
                        variant="ghost"
                        size="icon"
                        class="h-8 w-8 rounded-full bg-background/80 backdrop-blur border shadow-sm
                               hover:bg-muted hover:border-primary/50 hover:shadow-md
                               transition-all duration-200"
                        @click="toggleFullscreen"
                    >
                        <Minimize2 v-if="isFullscreen" class="size-4"/>
                        <Maximize2 v-else class="size-4"/>
                        <span class="sr-only">{{ isFullscreen ? 'Minimize' : 'Maximize' }}</span>
                    </Button>
                    <SheetClose as-child>
                        <Button
                            variant="ghost"
                            size="icon"
                            class="h-8 w-8 rounded-full bg-background/80 backdrop-blur border shadow-sm
                                   hover:bg-muted hover:border-primary/50 hover:shadow-md
                                   transition-all duration-200"
                        >
                            <X class="size-4"/>
                            <span class="sr-only">Close</span>
                        </Button>
                    </SheetClose>
                </div>
                <!-- Left panel: requests list (1/3) -->
                <div class="w-1/3 border-r bg-background">
                    <ScrollArea class="h-full">
                        <div class="p-1 border-b sticky top-0 bg-background z-10">
                            <SheetHeader class="py-2">
                                <SheetTitle class="flex items-center gap-2 text-lg">
                                    <Bug class="size-5"/>
                                    Debug Requests
                                </SheetTitle>
                                <SheetDescription>
                                    Total requests: {{ debugList.length }}
                                </SheetDescription>
                            </SheetHeader>
                        </div>
                        <div class="p-2 space-y-1">
                            <div
                                v-for="debug in debugList"
                                :key="debug.id"
                                @click="selectDebug(debug.id)"
                                class="p-3 rounded-lg cursor-pointer transition-colors hover:bg-muted"
                                :class="{ 'bg-muted border border-primary': selectedId === debug.id }"
                            >
                                <!-- Первая строка: метод + URL + статус -->
                                <div class="flex items-center justify-between mb-1">
                                    <div class="flex items-center gap-2 min-w-0">
                                        <Badge variant="info">{{ debug.request?.requestMethod || 'GET' }}</Badge>
                                        <span class="text-sm font-medium truncate">
                                            {{ debug.currentRoute?.uri || debug.request?.requestPath || '' }}
                                        </span>
                                    </div>
                                    <div v-if="debug.request?.responseStatusCode" class="shrink-0 ml-2">
                                        <Badge
                                            :variant="debug.request.responseStatusCode === 200 ? 'success':'destructive'">
                                            {{ debug.request.responseStatusCode }}
                                        </Badge>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3 text-xs text-muted-foreground">
                                    <span v-if="debug.app?.requestProcessingTime" class="flex items-center gap-1">
                                        <Clock class="size-3"/>
                                        {{ formatMs(debug.app.requestProcessingTime) }}
                                    </span>
                                    <span v-if="debug.app?.memoryUsage" class="flex items-center gap-1">
                                        <Cpu class="size-3"/>
                                        {{ formatBytes(debug.app.memoryUsage) }}
                                    </span>
                                    <span class="flex items-center gap-1">
                                        <Database class="size-3"/>
                                        {{ debug.queries?.length || 0 }} queries
                                    </span>
                                </div>
                            </div>
                        </div>
                    </ScrollArea>
                </div>

                <!-- Right panel: details (2/3) -->
                <div class="w-2/3 bg-background">
                    <ScrollArea class="h-full">
                        <DebugDetail v-if="selectedDebug" :debug="selectedDebug"/>
                        <!-- Empty state -->
                        <div v-else class="flex items-center justify-center h-full text-muted-foreground">
                            <div class="text-center">
                                <Bug class="size-12 mx-auto mb-3 opacity-20"/>
                                <p>Select a request from the list</p>
                            </div>
                        </div>
                    </ScrollArea>
                </div>
            </div>
        </SheetContent>
    </Sheet>
</template>
