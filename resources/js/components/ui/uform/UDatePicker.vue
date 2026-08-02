<script setup lang="ts">
import {CalendarDate, getLocalTimeZone, today} from '@internationalized/date'
import {CalendarIcon} from '@lucide/vue'
import {computed} from 'vue'
import {Button} from '@/js/components/ui/button'
import {Calendar} from '@/js/components/ui/calendar'
import {Popover, PopoverContent, PopoverTrigger,} from '@/js/components/ui/popover'
import {cn} from '@/js/lib/utils'
import type {DateValue} from "reka-ui"

const props = withDefaults(defineProps<{
    placeholder?: string,
    type?: 'date' | 'date-time',
}>(), {
    type: 'date',
});

const modelValue = defineModel<string | null>()

const currentDate = computed<Date | null>(() => {
    return modelValue.value ? new Date(modelValue.value) : null;
});

const dateValue = computed<DateValue | undefined>({
    get(): DateValue | undefined {
        if (!modelValue.value) return undefined

        const date = new Date(modelValue.value);
        return new CalendarDate(
            date.getUTCFullYear(),      // UTC
            date.getUTCMonth() + 1,     // UTC
            date.getUTCDate()           // UTC
        );
    },
    set(newValue: DateValue | undefined): void {
        if (!newValue) {
            modelValue.value = null
            return
        }

        if (props.type === 'date-time') {
            // Берем существующее время из currentDate или ставим полночь
            const hours = currentDate.value?.getUTCHours() ?? 0;
            const minutes = currentDate.value?.getUTCMinutes() ?? 0;
            const utcDate = new Date(Date.UTC(
                newValue.year,
                newValue.month - 1,
                newValue.day,
                hours,
                minutes
            ));
            modelValue.value = utcDate.toISOString();
        } else {
            modelValue.value = `${newValue.year}-${String(newValue.month).padStart(2, '0')}-${String(newValue.day).padStart(2, '0')}`;
        }
    }
});

const timeValue = computed({
    get() {
        if (!modelValue.value) return null;
        const date = new Date(modelValue.value);
        return `${String(date.getUTCHours()).padStart(2, '0')}:${String(date.getUTCMinutes()).padStart(2, '0')}`
    },
    set(value: string | null) {
        if (!value || !modelValue.value) return;
        const [hours, minutes] = value.split(':').map(Number);
        const date = new Date(modelValue.value);
        const utcDate = new Date(Date.UTC(
            date.getUTCFullYear(),
            date.getUTCMonth(),
            date.getUTCDate(),
            hours,
            minutes
        ));
        modelValue.value = utcDate.toISOString();
    }
});

// Плейсхолдер для календаря
const defaultPlaceholder = computed(() => {
    if (modelValue.value && dateValue.value) {
        return dateValue.value
    }
    return today(getLocalTimeZone())
})

// Форматирование отображаемой даты
const formattedDate = computed(() => {
    if (!currentDate.value) return props.placeholder || "Pick a date";
    const formatter = new Intl.DateTimeFormat('en-EN', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        ...(props.type === 'date-time' && {
            timeZone: 'UTC',
            hour12: false,
            hour: '2-digit',
            minute: '2-digit'
        })
    })

    return formatter.format(currentDate.value)
})
</script>

<template>
    <Popover>
        <PopoverTrigger as-child>
            <Button
                variant="outline"
                :class="cn(
                    'w-full justify-start text-left font-normal',
                    !modelValue && 'text-muted-foreground',
                )"
            >
                <CalendarIcon class="mr-2 h-4 w-4"/>
                {{ formattedDate }}
            </Button>
        </PopoverTrigger>
        <PopoverContent class="w-auto p-0">
            <Calendar
                v-model="dateValue"
                :initial-focus="true"
                :default-placeholder="defaultPlaceholder"
                layout="month-and-year"
            />

            <!-- Добавляем выбор времени для type="date-time" -->
            <div v-if="type === 'date-time' && dateValue" class="border-t p-3">
                <input
                    type="time"
                    v-model="timeValue"
                    class="w-full rounded border px-2 py-1 text-sm"
                />
            </div>
        </PopoverContent>
    </Popover>
</template>
