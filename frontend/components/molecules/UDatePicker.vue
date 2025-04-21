<script setup lang="ts">
    import { ref, computed, watch, onMounted, onUnmounted } from 'vue';
    import CalendarIcon from '../atoms/icons/CalendarIcon.vue';

    interface DatePickerProps {
        modelValue?: Date | null;
        label?: string;
        placeholder?: string;
        minDate?: Date;
        maxDate?: Date;
        dateFormat?: string;
        required?: boolean;
        disabled?: boolean;
        hintText?: string;
        destructive?: boolean;
        name?: string;
    }

    const props = withDefaults(defineProps<DatePickerProps>(), {
        modelValue: null,
        label: '',
        placeholder: 'Sélectionner une date',
        minDate: undefined,
        maxDate: undefined,
        dateFormat: 'dd/MM/yyyy',
        required: false,
        disabled: false,
        hintText: '',
        destructive: false,
        name: '',
    });

    const emit = defineEmits<{
        'update:modelValue': [date: Date | null];
        'invalid-date': [value: string];
    }>();

    const isOpen = ref(false);
    const inputValue = ref('');
    const pickerRef = ref<HTMLElement | null>(null);

    function setupClickOutside() {
        const handleClickOutside = (event: MouseEvent) => {
            if (pickerRef.value && !pickerRef.value.contains(event.target as Node)) {
                isOpen.value = false;
            }
        };

        onMounted(() => {
            document.addEventListener('click', handleClickOutside);
        });

        onUnmounted(() => {
            document.removeEventListener('click', handleClickOutside);
        });
    }

    setupClickOutside();

    const selected = computed({
        get: () => props.modelValue,
        set: (value) => {
            emit('update:modelValue', value);
            isOpen.value = false;
        },
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

    function toggleCalendar() {
        if (props.disabled) return;
        isOpen.value = !isOpen.value;
    }

    function _handleInputChange(e: Event) {
        if (props.disabled) return;

        const target = e.target as HTMLInputElement;
        inputValue.value = target.value;
    }

    function handleInputBlur() {
        const date = parseDate(inputValue.value);

        if (date) {
            selected.value = date;
        } else if (inputValue.value && inputValue.value !== '') {
            emit('invalid-date', inputValue.value);
        }
    }

    function _handleKeyDown(e: KeyboardEvent) {
        if (e.key === 'Escape') {
            isOpen.value = false;
        } else if (e.key === 'Enter') {
            handleInputBlur();
            isOpen.value = false;
        } else if (e.key === ' ' && !inputValue.value) {
            e.preventDefault();
            isOpen.value = true;
        }
    }

    watch(
        () => props.modelValue,
        (newValue) => {
            inputValue.value = formatDate(newValue);
        },
        { immediate: true }
    );

    // Ensure calendar closes when pressing escape
    onMounted(() => {
        document.addEventListener('keydown', (e: KeyboardEvent) => {
            if (e.key === 'Escape' && isOpen.value) {
                isOpen.value = false;
            }
        });
    });

    onUnmounted(() => {
        document.removeEventListener('keydown', (e: KeyboardEvent) => {
            if (e.key === 'Escape' && isOpen.value) {
                isOpen.value = false;
            }
        });
    });
</script>

<template>
    <div ref="pickerRef" class="relative w-full">
        <div class="flex flex-col gap-1.5 w-full">
            <label v-if="label" class="text-secondary text-sm font-medium" :for="name">
                {{ label }} <span v-if="required" class="text-brand-tertiary">*</span>
            </label>
            <UButton
                variant="secondary"
                :icon="CalendarIcon"
                icon-position="leading"
                :disabled="disabled"
                class="!justify-start"
                @click="toggleCalendar"
                >{{ inputValue || placeholder }}</UButton
            >
            <p v-if="hintText" :class="['text-sm', destructive ? 'text-error-primary' : 'text-tertiary']">
                {{ hintText }}
            </p>
        </div>

        <Transition name="dropdown">
            <div v-if="isOpen" class="absolute z-50 mt-1">
                <UCalendar
                    v-model="selected"
                    :min-date="minDate"
                    :max-date="maxDate"
                    :date-format="dateFormat"
                    @invalid-date="(value) => $emit('invalid-date', value)"
                />
            </div>
        </Transition>
    </div>
</template>

<style scoped>
    .dropdown-enter-active,
    .dropdown-leave-active {
        transition:
            opacity 0.2s ease,
            transform 0.2s ease;
    }

    .dropdown-enter-from,
    .dropdown-leave-to {
        opacity: 0;
        transform: translateY(-10px);
    }
</style>
