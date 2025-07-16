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
        'flex w-full border bg-primary rounded-lg focus:ring-2 focus:border-transparent focus:outline-none placeholder:text-placeholder text-primary shadow-xs text-center';

    const variantClasses = {
        default: 'border-primary focus:ring-primary',
        destructive: 'border-error-subtle focus:ring-error',
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
        <label v-if="label" class="text-secondary text-sm font-medium" :for="name">
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
                    disabled ? 'text-fg-disabled cursor-not-allowed' : 'text-secondary hover:text-secondary-hover',
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
                    disabled ? 'text-fg-disabled cursor-not-allowed' : 'text-secondary hover:text-secondary-hover',
                ]"
                :disabled="disabled || (max !== undefined && modelValue >= max)"
                aria-label="Increase value"
                @click="increment"
            >
                <PlusIcon />
            </button>
        </div>
        <p v-if="hintText" :class="['text-sm', destructive ? 'text-error-primary' : 'text-tertiary']">
            {{ hintText }}
        </p>
    </div>
</template>
