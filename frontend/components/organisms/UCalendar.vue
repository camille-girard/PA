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
const startsWithMonday = computed(() => !locale.value.startsWith('en'));

const orderedDayNames = computed(() => {
  const days = [...props.dayNames];
  return startsWithMonday.value ? [...days.slice(1), days[0]] : days;
});

function formatDate(date: Date | null): string {
  if (!date) return '';
  const day = String(date.getDate()).padStart(2, '0');
  const month = String(date.getMonth() + 1).padStart(2, '0');
  const year = date.getFullYear();
  return props.dateFormat.replace('dd', day).replace('MM', month).replace('yyyy', String(year));
}

function parseDate(dateString: string): Date | null {
  const parts = dateString.split('/');
  if (parts.length !== 3) return null;
  const [day, month, year] = parts.map(Number);
  const date = new Date(year, month - 1, day);
  return (date.getDate() === day && date.getMonth() === month - 1 && date.getFullYear() === year) ? date : null;
}

const monthYear = computed(() => capitalizeFirstLetter(d(current.value, { year: 'numeric', month: 'long' })));
const daysInMonth = computed(() => Array.from({ length: new Date(current.value.getFullYear(), current.value.getMonth() + 1, 0).getDate() }, (_, i) => i + 1));
const blanks = computed(() => Array((startsWithMonday.value ? (current.value.getDay() || 7) - 1 : current.value.getDay())).fill(0));

watch(() => props.modelValue, (newValue) => { dateInputValue.value = formatDate(newValue); }, { immediate: true });

const monthKey = computed(() => `${current.value.getFullYear()}-${current.value.getMonth()}`);
function prevMonth() { direction.value = 'prev'; current.value = new Date(current.value.getFullYear(), current.value.getMonth() - 1, 1); emit('month-change', current.value); }
function nextMonth() { direction.value = 'next'; current.value = new Date(current.value.getFullYear(), current.value.getMonth() + 1, 1); emit('month-change', current.value); }
function selectDate(day: number) { const newDate = new Date(current.value.getFullYear(), current.value.getMonth(), day); if (props.minDate && newDate < props.minDate || props.maxDate && newDate > props.maxDate) return; selected.value = newDate; }
function goToToday() { const todayMonth = today.value.getMonth(), currentMonth = current.value.getMonth(); direction.value = todayMonth > currentMonth ? 'next' : 'prev'; current.value = new Date(today.value.getFullYear(), today.value.getMonth(), 1); selected.value = new Date(today.value); emit('month-change', current.value); }
function isToday(day: number) { const t = today.value; return day === t.getDate() && current.value.getMonth() === t.getMonth() && current.value.getFullYear() === t.getFullYear(); }
function isSelected(day: number) { return selected.value?.getDate() === day && selected.value?.getMonth() === current.value.getMonth() && selected.value?.getFullYear() === current.value.getFullYear(); }
function isDisabled(day: number) { const date = new Date(current.value.getFullYear(), current.value.getMonth(), day); return (props.minDate && date < props.minDate) || (props.maxDate && date > props.maxDate); }
function handleDateInput() { const date = parseDate(dateInputValue.value); if (date) { selected.value = date; if (date.getMonth() !== current.value.getMonth() || date.getFullYear() !== current.value.getFullYear()) { direction.value = (date > current.value) ? 'next' : 'prev'; current.value = new Date(date.getFullYear(), date.getMonth(), 1); emit('month-change', current.value); } } else if (dateInputValue.value) emit('invalid-date', dateInputValue.value); }
</script>

<template>
  <div class="min-w-82 w-fit bg-white rounded-2xl border border-gray-200 shadow-xl px-6 py-5 relative z-50">
    <div class="flex items-center justify-between mb-3">
      <UButton variant="tertiary" size="sm" @click="prevMonth">
        <ChevronLeftIcon class="text-gray-400" />
      </UButton>
      <transition :name="direction === 'next' ? 'slide-up' : 'slide-down'" mode="out-in">
        <div :key="monthKey" class="font-semibold text-sm text-gray-800">
          {{ monthYear }}
        </div>
      </transition>
      <UButton variant="tertiary" size="sm" @click="nextMonth">
        <ChevronRightIcon class="text-gray-400" />
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
      <UButton v-if="showTodayButton" variant="secondary" size="md" @click="goToToday">
        {{ todayButtonText }}
      </UButton>
    </div>
    <div class="grid grid-cols-7">
      <p v-for="day in orderedDayNames" :key="day" class="text-gray-500 text-sm flex items-center justify-center size-10">
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
                'flex items-center justify-center size-10 rounded-full text-sm',
                isToday(day) ? 'bg-orange-100 text-orange-700 font-medium' : '',
                isSelected(day) ? 'bg-orange-600 text-white' : 'hover:bg-orange-50',
                isDisabled(day) ? 'opacity-40 cursor-not-allowed' : 'cursor-pointer'
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
.calendar-container { position: relative; }
.slide-left-enter-active, .slide-left-leave-active, .slide-right-enter-active, .slide-right-leave-active {
  transition: all 0.3s ease;
}
.slide-left-enter-from { transform: translateX(100%); opacity: 0; }
.slide-left-leave-to { transform: translateX(-100%); opacity: 0; }
.slide-right-enter-from { transform: translateX(-100%); opacity: 0; }
.slide-right-leave-to { transform: translateX(100%); opacity: 0; }
.slide-up-enter-active, .slide-up-leave-active, .slide-down-enter-active, .slide-down-leave-active {
  transition: all 0.2s ease;
}
.slide-up-enter-from { transform: translateY(20px); opacity: 0; }
.slide-up-leave-to { transform: translateY(-20px); opacity: 0; }
.slide-down-enter-from { transform: translateY(-20px); opacity: 0; }
.slide-down-leave-to { transform: translateY(20px); opacity: 0; }
</style>
