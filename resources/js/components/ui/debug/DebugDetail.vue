<script setup lang="ts">

import {Cpu, Database, Globe, Route} from "lucide-vue-next";
import {Accordion, AccordionContent, AccordionItem, AccordionTrigger} from "@js/components/ui/accordion/index.ts";
import {computed, ref} from "vue";
import {formatBytes, formatMs, highlightSql} from "@js/components/ui/debug";
import {Badge} from "@js/components/ui/badge";

const props = defineProps<{
    debug: DebugLog;
}>();

const totalTime = computed<string>(() => {
    if (!props.debug?.queries) return '0';
    return props.debug.queries.reduce((sum: number, q: DebugQuery) => sum + (q.duration || 0), 0).toFixed(2);
});

const expandedTraces = ref<Record<number, boolean>>({});

const toggleTrace = (queryIndex: number): void => {
    expandedTraces.value[queryIndex] = !expandedTraces.value[queryIndex];
};

const truncateSql = (sql: string | undefined, maxLength: number = 100): string => {
    if (!sql) return '';
    const singleLine = sql.replace(/\s+/g, ' ').trim();
    return singleLine.length > maxLength
        ? singleLine.substring(0, maxLength) + '…'
        : singleLine;
};

const getDurationClass = (durationInMs: number): string => {
    if (durationInMs < 30) {
        return 'text-emerald-600 dark:text-emerald-400';
    } else if (durationInMs < 100) {
        return 'text-green-600 dark:text-green-400';
    } else if (durationInMs < 300) {
        return 'text-amber-500 dark:text-amber-400';
    } else if (durationInMs < 1000) {
        return 'text-orange-500 dark:text-orange-400';
    } else {
        return 'text-red-600 dark:text-red-500';
    }
};
</script>

<template>
    <div class="p-6">
        <!-- Header -->
        <div class="mb-6">
            <h2 class="text-xl font-bold mb-2 flex items-center gap-2">
                <Badge variant="info">{{ debug.request?.requestMethod || 'GET' }}</Badge>
                {{ debug.currentRoute?.uri || debug.request?.requestPath || '' }}
            </h2>
            <div class="flex items-center gap-4 text-sm text-muted-foreground">
                <div v-if="debug.request?.responseStatusCode" class="flex items-center gap-1">
                    Status:
                    <Badge :variant="debug.request.responseStatusCode === 200 ? 'success':'destructive'">
                        {{ debug.request.responseStatusCode }}
                    </Badge>
                </div>
                <div v-if="debug.app?.requestProcessingTime">
                    Time: {{ formatMs(debug.app.requestProcessingTime) }}
                </div>
            </div>
        </div>

        <!-- Accordion sections -->
        <Accordion type="multiple" class="w-full" :default-value="['queries']">
            <!-- SQL Queries -->
            <AccordionItem v-if="debug.queries?.length" value="queries">
                <AccordionTrigger>
                    <div class="flex items-center gap-2">
                        <Database class="size-4"/>
                        SQL Queries ({{ debug.queries.length }})
                    </div>
                </AccordionTrigger>
                <AccordionContent>
                    <div class="text-sm text-muted-foreground mb-4">Total time: {{ totalTime }}ms</div>
                    <!-- Queries list with nested accordion -->
                    <Accordion type="single" collapsible class="space-y-2">
                        <AccordionItem
                            v-for="(query, index) in debug.queries"
                            :key="index" :value="`query-${index}`" class="border rounded-lg"
                        >
                            <!-- Compact query row -->
                            <AccordionTrigger class="px-3 py-2 hover:no-underline">
                                <div class="flex items-center gap-2 flex-1">
                                    <span :class="getDurationClass(query.duration)"
                                          class="text-xs font-mono w-16 shrink-0 text-left">
                                        {{ query.duration }}ms</span>
                                    <span class="text-xs text-muted-foreground w-20 shrink-0 text-left">
                                        {{ query.connection }}</span>
                                    <span class="text-xs truncate flex-1 font-mono text-left"
                                          v-html="highlightSql(truncateSql(query.sql))"
                                    />
                                </div>
                            </AccordionTrigger>
                            <!-- Expanded query details -->
                            <AccordionContent class="px-3 pb-3 space-y-3">
                                <!-- SQL -->
                                <div class="pt-3">
                                    <div class="text-xs text-muted-foreground mb-1 font-semibold">SQL</div>
                                    <pre
                                        class="text-xs bg-muted p-2 rounded whitespace-pre-wrap overflow-x-auto select-text"
                                        v-html="highlightSql(query.sql_formatted || query.sql)"
                                    />
                                </div>

                                <!-- Params -->
                                <div v-if="query.params && Object.keys(query.params).length">
                                    <div class="text-xs text-muted-foreground mb-1 font-semibold">Params</div>
                                    <pre class="text-xs bg-muted p-2 rounded">{{
                                            JSON.stringify(query.params, null, 2)
                                        }}</pre>
                                </div>

                                <!-- Stack Trace -->
                                <div v-if="query.trace?.length">
                                    <div
                                        class="flex items-center gap-1 text-xs text-muted-foreground cursor-pointer hover:text-foreground font-semibold"
                                        @click.stop="toggleTrace(index)"
                                    >
                                        <span class="transition-transform inline-block"
                                              :class="{ 'rotate-90': expandedTraces[index] }">▶</span>
                                        Stack trace ({{ query.trace.length }} frames)
                                    </div>
                                    <div
                                        v-if="expandedTraces[index]"
                                        class="mt-1 text-xs font-mono bg-muted p-2 rounded space-y-0.5 max-h-60 overflow-auto"
                                    >
                                        <div
                                            v-for="(item, i) in query.trace"
                                            :key="i"
                                            class="flex gap-2 hover:bg-muted-foreground/10 px-1 py-0.5 rounded"
                                        >
                                            <span class="text-gray-400 w-6 shrink-0 text-right">#{{ i }}</span>
                                            <span class="text-amber-400">{{ item.class }}{{ item.type }}{{
                                                    item.function
                                                }}()</span>
                                            <span v-if="item.file" class="text-gray-500 ml-auto shrink-0">{{
                                                    item.file
                                                }}:{{ item.line }}</span>
                                        </div>
                                    </div>
                                </div>
                            </AccordionContent>
                        </AccordionItem>
                    </Accordion>
                </AccordionContent>
            </AccordionItem>

            <!-- Application -->
            <AccordionItem v-if="debug.app" value="app">
                <AccordionTrigger>
                    <div class="flex items-center gap-2">
                        <Cpu class="size-4"/>
                        Application
                    </div>
                </AccordionTrigger>
                <AccordionContent>
                    <div class="grid grid-cols-2 gap-2 text-sm pl-6">
                        <div class="flex justify-between">
                            <span class="text-muted-foreground">Memory:</span>
                            <span class="font-mono">{{ formatBytes(debug.app.memoryUsage) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-muted-foreground">Peak Memory:</span>
                            <span class="font-mono">{{ formatBytes(debug.app.memoryPeakUsage) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-muted-foreground">Request Time:</span>
                            <span class="font-mono">{{ formatMs(debug.app.requestProcessingTime) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-muted-foreground">Application Emit:</span>
                            <span class="font-mono">{{ formatMs(debug.app.applicationEmit) }}</span>
                        </div>
                    </div>
                </AccordionContent>
            </AccordionItem>

            <!-- Route -->
            <AccordionItem v-if="debug.currentRoute" value="route">
                <AccordionTrigger>
                    <div class="flex items-center gap-2">
                        <Route class="size-4"/>
                        Route
                    </div>
                </AccordionTrigger>
                <AccordionContent>
                    <div class="space-y-2 pl-6">
                        <div class="grid grid-cols-2 gap-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-muted-foreground">Name:</span>
                                <span class="font-mono">{{ debug.currentRoute.name }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-muted-foreground">Pattern:</span>
                                <span class="font-mono">{{ debug.currentRoute.pattern }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-muted-foreground">Match Time:</span>
                                <span class="font-mono">{{ formatMs(debug.currentRoute.matchTime) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-muted-foreground">URI:</span>
                                <span class="font-mono text-xs truncate max-w-[200px]">{{
                                        debug.currentRoute.uri
                                    }}</span>
                            </div>
                        </div>
                        <div v-if="debug.currentRoute.middlewares?.length" class="mt-2">
                            <div class="text-xs text-muted-foreground mb-1">Middlewares:</div>
                            <div class="flex flex-wrap gap-1">
                                                        <span
                                                            v-for="middleware in debug.currentRoute.middlewares"
                                                            :key="middleware"
                                                            class="px-2 py-0.5 bg-muted rounded text-xs font-mono"
                                                        >
                                                            {{ middleware }}
                                                        </span>
                            </div>
                        </div>
                    </div>
                </AccordionContent>
            </AccordionItem>

            <!-- Request -->
            <AccordionItem v-if="debug.request" value="request">
                <AccordionTrigger>
                    <div class="flex items-center gap-2">
                        <Globe class="size-4"/>
                        Request
                    </div>
                </AccordionTrigger>
                <AccordionContent>
                    <div class="space-y-3 pl-6">
                        <div class="grid grid-cols-2 gap-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-muted-foreground">IP:</span>
                                <span class="font-mono">{{ debug.request.userIp }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-muted-foreground">Method:</span>
                                <span class="font-mono">{{ debug.request.requestMethod }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-muted-foreground">Status:</span>
                                <span class="font-mono">{{ debug.request.responseStatusCode }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-muted-foreground">AJAX:</span>
                                <span class="font-mono">{{ debug.request.requestIsAjax ? 'Yes' : 'No' }}</span>
                            </div>
                        </div>
                        <div v-if="debug.request.requestRaw">
                            <div class="text-xs text-muted-foreground mb-1 font-semibold">Request Headers:</div>
                            <pre
                                class="text-xs bg-muted p-2 rounded overflow-auto max-h-40 font-mono whitespace-pre-wrap">{{
                                    debug.request.requestRaw
                                }}</pre>
                        </div>
                    </div>
                </AccordionContent>
            </AccordionItem>
        </Accordion>
    </div>
</template>

<style scoped>

</style>
