<script setup lang="ts">
import {Tooltip, TooltipContent, TooltipProvider, TooltipTrigger} from '@js/components/ui/tooltip';
import {User} from '@js/types/user.ts';
import {computed} from "vue";
import UserAvatar from "@js/components/user/UserAvatar.vue";

const props = withDefaults(defineProps<{
    users: User[]
    max?: number
}>(), {
    max: 4,
});

const maxUsers = computed(() => props.max);

const getProfileUrl = (id: number) => `/user/show/${id}`;
</script>

<template>
    <TooltipProvider>
        <div class="flex -space-x-2.5">
            <Tooltip v-for="user in users.slice(0, maxUsers)" :key="user.id">
                <TooltipTrigger>
                    <Link :href="getProfileUrl(user.id)"
                          class="block rounded-full transition-transform hover:z-10 hover:scale-110 relative"
                          style="z-index: 1;"
                    >
                        <div class="rounded-full ring-2 ring-white dark:ring-neutral-800">
                            <UserAvatar
                                :user="user"
                                class="size-8 transition-all"
                            />
                        </div>
                    </Link>
                </TooltipTrigger>
                <TooltipContent align="start">
                    <p>{{ user.fullName }}</p>
                </TooltipContent>
            </Tooltip>
            <Tooltip v-if="users.length > maxUsers">
                <TooltipTrigger>
                    <div
                        class="relative flex items-center justify-center size-8 rounded-full bg-muted ring-2 ring-white dark:ring-neutral-800 text-xs text-muted-foreground z-10 hover:scale-110 cursor-pointer">
                        +{{ users.length - maxUsers }}
                    </div>
                </TooltipTrigger>
                <TooltipContent align="start">
                    <p>{{ users.length - maxUsers }} more</p>
                </TooltipContent>
            </Tooltip>
        </div>
    </TooltipProvider>
</template>
