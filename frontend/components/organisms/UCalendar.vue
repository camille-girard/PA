<script setup lang="ts">
    import { capitalizeFirstLetter } from '~/utils/string';

    interface CalendarProps {
        modelValue?: Date | null;
        todayDate?: Date;
        dayNames?: string[];
        showTodayButton?: boolean;
        showDateInput?: boolean;
        minDate?: Date;
        maxDate?: Date;
        dateFormat?: string;
        inputPlaceholder?: string;
        todayButtonText?: string;
    }

    const props = withDefaults(defineProps<CalendarProps>(), {
        modelValue: null,
        todayDate: () => new Date(),
        dayNames: () => ['Dim', 'Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam'],
        showTodayButton: true,
        showDateInput: true,
        minDate: undefined,
        maxDate: undefined,
        dateFormat: 'dd/MM/yyyy',
        inputPlaceholder: 'JJ/MM/AAAA',
        todayButtonText: "Aujourd'hui",
    });

    const emit = defineEmits<{
        'update:modelValue': [date: Date | null];
        'month-change': [date: Date];
        'invalid-date': [value: string];
    }>();

    const today = computed(() => props.todayDate);
    const selected = computed({
        get: () => props.modelValue,
        set: (value) => emit('update:modelValue', value),
    });
    const current = ref(
        new Date(
            props.modelValue?.getFullYear() || today.value.getFullYear(),
            props.modelValue?.getMonth() || today.value.getMonth(),
            1
        )
    );

    const { d, locale } = useI18n();

    const dateInputValue = ref('');

    const direction = ref<'next' | 'prev'>('next');

    const startsWithMonday = computed(() => {
        return !locale.value.startsWith('en');
    });

    const orderedDayNames = computed(() => {
        if (startsWithMonday.value) {
            const days = [...props.dayNames];
            return [...days.slice(1), days[0]];
        }
        return props.dayNames;
    });

    function formatDate(date: Date | null): string {
        if (!date) return '';

        const day = String(date.getDate()).padStart(2, '0');
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const year = date.getFullYear();

        return props.dateFormat.replace('dd', day).replace('MM', month).replace('yyyy', String(year));
    }

    function parseDate(dateString: string): Date | null {
        if (!dateString) return null;

        if (props.dateFormat === 'dd/MM/yyyy') {
            const parts = dateString.split('/');
            if (parts.length !== 3) return null;

            const day = parseInt(parts[0], 10);
            const month = parseInt(parts[1], 10) - 1;
            const year = parseInt(parts[2], 10);

            if (isNaN(day) || isNaN(month) || isNaN(year)) return null;

            const date = new Date(year, month, day);

            if (date.getDate() !== day || date.getMonth() !== month || date.getFullYear() !== year) {
                return null;
            }

            return date;
        }

        return null;
    }

    const monthYear = computed(() => capitalizeFirstLetter(d(current.value, { year: 'numeric', month: 'long' })));

    const daysInMonth = computed(() => {
        const year = current.value.getFullYear();
        const month = current.value.getMonth();
        return Array.from(
            {
                length: new Date(year, month + 1, 0).getDate(),
            },
            (_, i) => i + 1
        );
    });

    const blanks = computed(() => {
        let firstDayOfMonth = current.value.getDay();

        if (startsWithMonday.value) {
            firstDayOfMonth = firstDayOfMonth === 0 ? 6 : firstDayOfMonth - 1;
        }

        return Array(firstDayOfMonth).fill(0);
    });

    watch(
        () => props.modelValue,
        (newValue) => {
            dateInputValue.value = formatDate(newValue);
        },
        { immediate: true }
    );

    const monthKey = computed(() => `${current.value.getFullYear()}-${current.value.getMonth()}`);

    function prevMonth() {
        direction.value = 'prev';
        current.value = new Date(current.value.getFullYear(), current.value.getMonth() - 1, 1);
        emit('month-change', new Date(current.value));
    }

    function nextMonth() {
        direction.value = 'next';
        current.value = new Date(current.value.getFullYear(), current.value.getMonth() + 1, 1);
        emit('month-change', new Date(current.value));
    }

    function selectDate(day: number) {
        const newDate = new Date(current.value.getFullYear(), current.value.getMonth(), day);

        if (props.minDate && newDate < props.minDate) return;
        if (props.maxDate && newDate > props.maxDate) return;

        selected.value = newDate;
    }

    function goToToday() {
        const newMonth = today.value.getMonth();
        const currentMonth = current.value.getMonth();

        direction.value = newMonth > currentMonth ? 'next' : 'prev';

        if (newMonth === currentMonth && today.value.getFullYear() !== current.value.getFullYear()) {
            direction.value = today.value.getFullYear() > current.value.getFullYear() ? 'next' : 'prev';
        }

        current.value = new Date(today.value.getFullYear(), today.value.getMonth(), 1);
        selected.value = new Date(today.value);
        emit('month-change', new Date(current.value));
    }

    function isToday(day: number) {
        return (
            day === today.value.getDate() &&
            current.value.getMonth() === today.value.getMonth() &&
            current.value.getFullYear() === today.value.getFullYear()
        );
    }

    function isSelected(day: number) {
        return (
            selected.value &&
            selected.value.getDate() === day &&
            selected.value.getMonth() === current.value.getMonth() &&
            selected.value.getFullYear() === current.value.getFullYear()
        );
    }

    function isDisabled(day: number) {
        const date = new Date(current.value.getFullYear(), current.value.getMonth(), day);

        if (props.minDate && date < props.minDate) return true;
        if (props.maxDate && date > props.maxDate) return true;

        return false;
    }

    function handleDateInput() {
        const date = parseDate(dateInputValue.value);

        if (date) {
            selected.value = date;

            const newMonth = date.getMonth();
            const currentMonth = current.value.getMonth();

            if (newMonth !== currentMonth || date.getFullYear() !== current.value.getFullYear()) {
                direction.value =
                    date.getFullYear() > current.value.getFullYear() ||
                    (date.getFullYear() === current.value.getFullYear() && newMonth > currentMonth)
                        ? 'next'
                        : 'prev';

                current.value = new Date(date.getFullYear(), date.getMonth(), 1);
                emit('month-change', new Date(current.value));
            }
        } else if (dateInputValue.value) {
            emit('invalid-date', dateInputValue.value);
        }
    }
</script>

<template>
    <div class="min-w-82 w-fit bg-primary rounded-2xl border border-secondary-alt shadow-xl px-6 py-5 relative z-50">
        <div class="flex items-center justify-between mb-3">
            <UButton variant="tertiary" size="sm" @click="prevMonth">
                <ChevronLeftIcon class="text-fg-quaternary" />
            </UButton>
            <transition :name="direction === 'next' ? 'slide-up' : 'slide-down'" mode="out-in">
                <div :key="monthKey" class="font-semibold text-sm text-secondary">
                    {{ monthYear }}
                </div>
            </transition>
            <UButton variant="tertiary" size="sm" @click="nextMonth">
                <ChevronRightIcon class="text-fg-quaternary" />
            </UButton>
        </div>
        <div v-if="showDateInput || showTodayButton" class="flex items-center gap-3 mb-3">
            <UInput
                v-if="showDateInput"
                v-model="dateInputValue"
                type="text"
                class="w-full"
                size="sm"
                :placeholder="inputPlaceholder"
                @blur="handleDateInput"
                @keyup.enter="handleDateInput"
            />
            <UButton v-if="showTodayButton" variant="secondary" size="md" @click="goToToday">{{
                todayButtonText
            }}</UButton>
        </div>
        <div class="grid grid-cols-7">
            <p
                v-for="day in orderedDayNames"
                :key="day"
                class="text-secondary text-sm flex items-center justify-center size-10"
            >
                {{ day }}
            </p>
        </div>
        <div class="calendar-container overflow-hidden">
            <transition :name="direction === 'next' ? 'slide-left' : 'slide-right'" mode="out-in">
                <div :key="monthKey" class="grid grid-cols-7">
                    <div v-for="blank in blanks" :key="'b' + blank" class="size-10" />
                    <button
                        v-for="day in daysInMonth"
                        :key="day"
                        :class="[
                            'flex items-center justify-center size-10 rounded-full text-sm text-secondary',
                            isToday(day) ? 'bg-active font-medium' : '',
                            isSelected(day) ? 'bg-brand-solid text-white' : 'hover:bg-primary-hover',
                            isDisabled(day) ? 'opacity-40 cursor-not-allowed' : 'cursor-pointer',
                        ]"
                        :disabled="isDisabled(day)"
                        @click="selectDate(day)"
                    >
                        {{ day }}
                    </button>
                </div>
            </transition>
        </div>
    </div>
</template>

<style scoped>
    .calendar-container {
        position: relative;
    }

    .slide-left-enter-active,
    .slide-left-leave-active,
    .slide-right-enter-active,
    .slide-right-leave-active {
        transition: all 0.3s ease;
    }

    .slide-left-enter-from {
        transform: translateX(100%);
        opacity: 0;
    }

    .slide-left-leave-to {
        transform: translateX(-100%);
        opacity: 0;
    }

    .slide-right-enter-from {
        transform: translateX(-100%);
        opacity: 0;
    }

    .slide-right-leave-to {
        transform: translateX(100%);
        opacity: 0;
    }

    .slide-up-enter-active,
    .slide-up-leave-active,
    .slide-down-enter-active,
    .slide-down-leave-active {
        transition: all 0.2s ease;
    }

    .slide-up-enter-from {
        transform: translateY(20px);
        opacity: 0;
    }

    .slide-up-leave-to {
        transform: translateY(-20px);
        opacity: 0;
    }

    .slide-down-enter-from {
        transform: translateY(-20px);
        opacity: 0;
    }

    .slide-down-leave-to {
        transform: translateY(20px);
        opacity: 0;
    }
</style>
