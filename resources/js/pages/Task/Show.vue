<script setup>
import DashboardLayout from "@js/layouts/DashboardLayout.vue";
import {UDetail, UDetailItem} from "@js/components/ui/udetail/index.ts";
import {Card, CardContent, CardFooter, CardHeader, CardTitle} from "@js/components/ui/card/index.ts";
import {UActionList} from "@js/components/ui/uaction/index.ts";
import TaskStatus from "@js/pages/Task/Components/TaskStatus.vue";
import {Tabs, TabsContent, TabsList, TabsTrigger} from "@js/components/ui/tabs/index.ts";
import {Clock2, ListPlusIcon, MessageSquare, OctagonAlert} from '@lucide/vue';
import {Alert, AlertDescription} from "@js/components/ui/alert/index.ts";
import {UTable, UTableColumn} from "@js/components/ui/utable/index.ts";
import UserInfo from "@js/components/user/UserInfo.vue";
import UList from "@js/components/ui/udata/UList.vue";
import CommentForm from "@js/pages/TaskComment/Components/CommentForm.vue";
import {Badge} from "@js/components/ui/badge/index.ts";
import TimeProgress from "@js/pages/Task/Components/TimeProgress.vue";

defineOptions({
    layout: DashboardLayout,
});
const props = defineProps({
    task: {
        type: Object,
        required: true,
    },
    actions: {
        type: Array,
        default: [],
    },
    timeEntryUrl: {
        type: String,
        required: true,
    },
    commentForm: {
        type: Object,
        required: true,
    },
    commentSaveUrl: {
        type: String,
        required: true,
    },
    commentDataUrl: {
        type: String,
        required: true,
    },
});
</script>

<template>
    <div class="flex flex-col lg:flex-row gap-4 mb-3">
        <div class="w-full lg:w-2/3 flex flex-col gap-2">
            <Card>
                <CardHeader class="flex flex-row items-center justify-between">
                    <CardTitle>{{ task.name }}</CardTitle>
                    <TaskStatus :task="task"/>
                </CardHeader>
                <CardContent>
                    <p class="whitespace-pre-line">{{ task.description }}</p>
                </CardContent>
                <CardFooter class="flex justify-end gap-2 border-t pt-4">
                    <UActionList :actions="actions"/>
                </CardFooter>
            </Card>
            <Tabs default-value="comments" class="w-full">
                <TabsList class="grid grid-cols-3">
                    <TabsTrigger value="comments" class="flex items-center gap-2 cursor-pointer">
                        <MessageSquare class="h-4 w-4"/>
                        Comments
                    </TabsTrigger>
                    <TabsTrigger value="time" class="flex items-center gap-2 cursor-pointer">
                        <Clock2 class="h-4 w-4"/>
                        Time Tracking
                    </TabsTrigger>
                    <TabsTrigger value="history" class="flex items-center gap-2 cursor-pointer">
                        <ListPlusIcon class="h-4 w-4"/>
                        History
                    </TabsTrigger>
                </TabsList>
                <TabsContent value="comments">
                    <UList :url="commentDataUrl" mode="lazy" class="flex flex-col gap-2"
                           paginationWrapperClass="rounded-lg" containerClass="flex flex-col gap-2"
                           listClass="flex flex-col gap-2">
                        <template #prepend>
                            <Card class="py-4">
                                <CardContent class="px-3">
                                    <CommentForm :form="commentForm" :saveUrl="commentSaveUrl" :withRefresh="true"/>
                                </CardContent>
                            </Card>
                        </template>
                        <template #empty>
                            <Alert variant="destructive">
                                <OctagonAlert class="h-4 w-4"/>
                                <AlertDescription>
                                    No comments yet. Be the first to share your thoughts!
                                </AlertDescription>
                            </Alert>
                        </template>
                        <template #default="{index, item, provider}">
                            <Card class="py-2 relative">
                                <CardContent class="px-3 flex flex-col gap-1">
                                    <UserInfo :user="item.user_r">
                                        <template #description>
                                            <div class="text-xs text-muted-foreground">{{ item.created_at }}</div>
                                        </template>
                                    </UserInfo>
                                    <div class="whitespace-pre-line">{{ item.comment }}</div>
                                </CardContent>
                                <div class="absolute top-3 right-3 z-10">
                                    <UActionList
                                        v-if="provider"
                                        :actions="provider.actions[index]"
                                        mode="compact"
                                    />
                                </div>
                            </Card>
                        </template>
                    </UList>
                </TabsContent>
                <TabsContent value="time">
                    <UTable :url="timeEntryUrl" mode="lazy">
                        <UTableColumn prop="user" label="User">
                            <template #default="{row}">
                                <UserInfo :user="row.user">
                                    <template #description>
                                        <div class="text-xs text-muted-foreground">{{ row.created_at }}</div>
                                    </template>
                                </UserInfo>
                            </template>
                        </UTableColumn>
                        <UTableColumn prop="duration" label="Duration"/>
                        <UTableColumn prop="description" label="Description"
                                      cellClass="max-w-[300px] whitespace-normal"/>
                        <UTableColumn prop="date" label="Work date"/>
                        <UTableColumn label="Actions">
                            <template #default="{ row, index, provider }">
                                <UActionList v-if="provider" :actions="provider.actions[index]" mode="compact"/>
                            </template>
                        </UTableColumn>
                    </UTable>
                </TabsContent>
                <TabsContent value="history">
                    <Alert variant="destructive">
                        <OctagonAlert class="h-4 w-4"/>
                        <AlertDescription>
                            This section is under development and will be available soon.
                        </AlertDescription>
                    </Alert>
                </TabsContent>
            </Tabs>
        </div>
        <div class="w-full lg:w-1/3 flex flex-col gap-4">
            <UDetail class="m-1" :model="task">
                <UDetailItem label="ID" name="id"/>
                <UDetailItem label="Project" name="project_r">
                    <template #default="{model}">
                        {{ model.project_r?.name }}
                    </template>
                </UDetailItem>
                <UDetailItem label="Deadline" name="deadline"/>
                <UDetailItem label="Estimated time" name="estimated_time">
                    <template #default="{ model }">
                        <Badge variant="secondary">{{ model.estimated_time }}</Badge>
                    </template>
                </UDetailItem>
                <UDetailItem label="Progress" name="progress">
                    <template #default="{ model }">
                        <TimeProgress :value="model.progress" :variant="model.progress >= 100 ? 'destructive':'success'"
                                      progressClass="h-4">{{ model.estimated_time }}/{{ model.spent_time }}
                        </TimeProgress>
                    </template>
                </UDetailItem>
                <UDetailItem label="Created at" name="created_at"/>
                <UDetailItem label="Updated at" name="updated_at"/>
            </UDetail>
            <Card class="gap-2 py-3">
                <CardHeader>
                    <CardTitle>Assignees</CardTitle>
                </CardHeader>
                <CardContent>
                    <div v-if="task.user_links_r?.length" class="flex flex-col gap-2">
                        <UserInfo v-for="userLink in task.user_links_r" :key="userLink.id" :user="userLink.user">
                            <template #description v-if="userLink.time_summary">
                                <TimeProgress :value="100" variant="success" class="max-w-[120px]">
                                    {{ userLink.time_summary.total_duration }}
                                </TimeProgress>
                            </template>
                        </UserInfo>
                    </div>
                    <p v-else class="text-muted-foreground text-sm">No assignees</p>
                </CardContent>
            </Card>
        </div>
    </div>
</template>

<style scoped>

</style>
