<script setup lang="ts">
    interface CheckboxProps {
        modelValue: (boolean | 'indeterminate') | boolean;
        disabled?: boolean;
        name: string;
        required?: boolean;
        size?: 'sm' | 'md';
        id?: string;
        label?: string;
        supportingLabel?: string; // Text under label
    }

    const props = withDefaults(defineProps<CheckboxProps>(), {
        disabled: false,
        required: false,
        size: 'sm',
        id: '',
        label: '',
        supportingLabel: '',
    });

    const isChecked = computed(() => !!props.modelValue && props.modelValue !== 'indeterminate');
    const isIndeterminate = computed(() => props.modelValue === 'indeterminate');

    const emit = defineEmits<{
        'update:modelValue': [value: boolean];
        change: [event: Event];
        click: [event: MouseEvent];
    }>();

    const updateValue = (event: Event) => {
        if (props.disabled) return;

        const target = event.target as HTMLInputElement;
        emit('update:modelValue', target.checked);
        emit('change', event);
    };

    const inputId = computed(() => props.id || `checkbox-${Math.random().toString(36).substring(2, 9)}`);

    const sizeClasses = {
        sm: 'w-4 h-4',
        md: 'w-5 h-5 mt-0.5',
    };

    const disabledClasses = 'bg-disabled-subtle border-disabled text-fg-disabled-subtle cursor-not-allowed';
</script>

<template>
    <label class="relative flex gap-2 cursor-pointer w-fit" :for="inputId">
        <input
            :id="inputId"
            :checked="!!modelValue"
            :indeterminate="modelValue === 'indeterminate'"
            :disabled="disabled"
            type="checkbox"
            :name="name"
            class="peer hidden"
            @change="updateValue"
            @click="$emit('click', $event)"
        />
        <div
            :class="[
                sizeClasses[size],
                'border border-primary rounded flex items-center justify-center focus:ring focus:ring-primary focus:outline-none',
                disabled ? disabledClasses : '',
                {
                    'border-0 bg-brand-solid': (isChecked || modelValue === 'indeterminate') && !disabled,
                },
            ]"
        >
            <svg
                v-if="isChecked"
                :class="[
                    'text-white transition-all duration-300',
                    {
                        'opacity-0': !isChecked,
                        'opacity-100': isChecked,
                        '!text-fg-disabled-subtle': disabled,
                        'w-3 h-3': size === 'sm',
                        'w-3.5 h-3.5': size === 'md',
                    },
                ]"
                xmlns="http://www.w3.org/2000/svg"
                width="12"
                height="12"
                viewBox="0 0 12 12"
                fill="none"
            >
                <path
                    d="M10 3L4.5 8.5L2 6"
                    stroke="currentColor"
                    stroke-width="1.6666"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                />
            </svg>
            <svg
                v-else-if="isIndeterminate"
                :class="[
                    'text-white',
                    {
                        'w-3 h-3': size === 'sm',
                        'w-3.5 h-3.5': size === 'md',
                        '!text-fg-disabled-subtle': disabled,
                    },
                ]"
                xmlns="http://www.w3.org/2000/svg"
                width="12"
                height="12"
                viewBox="0 0 12 12"
                fill="none"
            >
                <path
                    d="M2.5 6H9.5"
                    stroke="currentColor"
                    stroke-width="1.66666"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                />
            </svg>
        </div>
        <div v-if="label">
            <p :class="['text-secondary font-medium', { 'text-xs': size === 'sm' }]">
                {{ label }}
            </p>
            <p v-if="supportingLabel" :class="['text-tertiary', { 'text-xs': size === 'sm' }]">
                {{ supportingLabel }}
            </p>
        </div>
    </label>
</template>
