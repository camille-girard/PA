<script setup lang="ts">
    interface SwitchProps {
        modelValue: boolean;
        size?: 'sm' | 'md';
        type?: 'default' | 'slim';
        disabled?: boolean;
        label?: string;
        supportingLabel?: string;
    }

    const props = withDefaults(defineProps<SwitchProps>(), {
        size: 'sm',
        type: 'default',
        disabled: false,
        label: '',
        supportingLabel: '',
    });

    const isChecked = computed(() => props.modelValue);

    const baseClasses = 'bg-tertiary rounded-full flex transition duration-300 ease-in-out h-fit mt-0.5';

    const sizeClasses = {
        sm: 'p-0.5 w-9',
        md: 'p-0.5 w-11',
    };

    const circleSizeClasses = {
        sm: 'size-4',
        md: 'size-5',
    };

    const emit = defineEmits<{
        'update:modelValue': [value: boolean];
    }>();

    function handleClick() {
        if (props.disabled) return;

        emit('update:modelValue', !props.modelValue);
    }
</script>

<template>
    <div class="flex gap-2">
        <button
            :class="[baseClasses, sizeClasses[size], { '!bg-brand-solid': isChecked, '!bg-disabled': disabled }]"
            @click="handleClick"
        >
            <span
                :class="[
                    circleSizeClasses[size],
                    'rounded-full flex-shrink-0 shadow-sm bg-white translate-x-0 transition-transform duration-200',
                    {
                        'translate-x-4': isChecked && size === 'sm',
                        'translate-x-5': isChecked && size === 'md',
                    },
                ]"
            />
        </button>
        <div v-if="label" :class="size === 'sm' ? 'text-sm' : 'text-base'">
            <label class="text-secondary font-medium" @click="handleClick">{{ label }}</label>
            <p v-if="supportingLabel" class="text-tertiary text-sm font-normal">
                {{ supportingLabel }}
            </p>
        </div>
    </div>
</template>
