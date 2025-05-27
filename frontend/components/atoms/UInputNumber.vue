<script setup lang="ts">
    interface InputNumberProps {
        modelValue?: number;
        min?: number;
        max?: number;
        step?: number;
        placeholder?: string;
        label?: string;
        size?: 'sm' | 'md';
        name?: string;
        required?: boolean;
        hintText?: string;
        destructive?: boolean;
        disabled?: boolean;
    }

    const props = withDefaults(defineProps<InputNumberProps>(), {
        modelValue: 0,
        min: undefined,
        max: undefined,
        step: 1,
        size: 'sm',
        label: '',
        placeholder: '',
        name: '',
        required: false,
        destructive: false,
        hintText: '',
        disabled: false,
    });

    const emit = defineEmits<{
        'update:modelValue': [value: number];
    }>();

    const inputValue = computed({
        get: () => props.modelValue,
        set: (value) => emit('update:modelValue', value),
    });

    const baseClasses =
        'flex w-full rounded-lg border border-gray-300 focus:ring-2 focus:outline-none placeholder:text-gray-400 text-gray-500 shadow-xs text-center';

    const variantClasses = {
        default: 'focus:border-orange-500 focus:ring-orange-500',
        destructive: 'border-error-subtle focus:ring-error focus:border-error-subtle',
        disabled: 'bg-disabled border-disabled-subtle text-fg-disabled cursor-not-allowed',
    };

    const sizeClasses = {
        sm: 'px-3 py-2',
        md: 'px-4 py-2.5',
    };

    const buttonPositionClasses = {
        left: 'absolute left-0 top-0 h-full flex items-center justify-center pl-2',
        right: 'absolute right-0 top-0 h-full flex items-center justify-center pr-2',
    };

    const buttonSizeClasses = {
        sm: 'size-6',
        md: 'size-7',
    };

    function increment() {
        if (props.disabled) return;

        let newValue = (inputValue.value || 0) + props.step;

        if (props.max !== undefined) {
            newValue = Math.min(newValue, props.max);
        }

        inputValue.value = newValue;
    }

    function decrement() {
        if (props.disabled) return;

        let newValue = (inputValue.value || 0) - props.step;

        if (props.min !== undefined) {
            newValue = Math.max(newValue, props.min);
        }

        inputValue.value = newValue;
    }

    function handleInput(event: Event) {
        if (props.disabled) return;

        const target = event.target as HTMLInputElement;
        let value = parseFloat(target.value);

        if (isNaN(value)) {
            inputValue.value = 0;
            return;
        }

        if (props.min !== undefined && value < props.min) {
            value = props.min;
        }

        if (props.max !== undefined && value > props.max) {
            value = props.max;
        }

        inputValue.value = value;
    }
</script>

<template>
    <div class="flex flex-col gap-1.5">
        <label v-if="label" class="text-body-sm" :for="name">
            {{ label }} <span v-if="required" class="text-brand-tertiary">*</span>
        </label>
        <div class="relative">
            <input
                :id="name"
                :class="[
                    baseClasses,
                    disabled ? variantClasses.disabled : variantClasses[destructive ? 'destructive' : 'default'],
                    sizeClasses[size],
                    'px-10',
                ]"
                type="number"
                :min="min"
                :max="max"
                :step="step"
                :placeholder="placeholder"
                :name="name"
                :value="modelValue"
                :disabled="disabled"
                @input="handleInput"
            />

            <button
                type="button"
                :class="[
                    buttonPositionClasses.left,
                    buttonSizeClasses[size],
                    disabled ? 'text-gray-300 cursor-not-allowed' : 'text-gray-500 hover:text-gray-700',
                ]"
                :disabled="disabled || (min !== undefined && modelValue <= min)"
                aria-label="Decrease value"
                @click="decrement"
            >
                <MinusIcon />
            </button>

            <button
                type="button"
                :class="[
                    buttonPositionClasses.right,
                    buttonSizeClasses[size],
                    disabled ? 'text-gray-300 cursor-not-allowed' : 'text-gray-500 hover:text-gray-700',
                ]"
                :disabled="disabled || (max !== undefined && modelValue >= max)"
                aria-label="Increase value"
                @click="increment"
            >
                <PlusIcon />
            </button>
        </div>
        <p v-if="hintText" :class="['text-body-sm', destructive ? 'text-error-primary' : 'text-body-sm']">
            {{ hintText }}
        </p>
    </div>
</template>
