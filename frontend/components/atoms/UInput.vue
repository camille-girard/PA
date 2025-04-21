<script setup lang="ts">
    import type { InputTypeHTMLAttribute } from 'vue';

    interface InputProps {
        placeholder?: string;
        label?: string;
        type: InputTypeHTMLAttribute;
        size?: 'sm' | 'md';
        name?: string;
        required?: boolean;
        hintText?: string;
        destructive?: boolean;
        modelValue?: string | number;
        icon?: Component | string | null;
        iconPosition?: 'leading' | 'trailing';
    }

    const _props = withDefaults(defineProps<InputProps>(), {
        size: 'sm',
        label: '',
        placeholder: '',
        name: '',
        required: false,
        destructive: false,
        hintText: '',
        modelValue: '',
        icon: null,
        iconPosition: 'leading',
    });

    const _emit = defineEmits<{
        'update:modelValue': [value: string | number];
    }>();

    const baseClasses =
        'flex gap-2 border bg-primary rounded-lg focus:ring-2 focus:border-transparent focus:outline-none placeholder:text-placeholder text-primary shadow-xs w-full';

    const variantClasses = {
        default: 'border-primary focus:ring-primary',
        destructive: 'border-error-subtle focus:ring-error',
    };

    const sizeClasses = {
        sm: 'px-3 py-2',
        md: '',
    };

    const wrapperClasses = 'flex items-center relative w-full';
</script>

<template>
    <div class="flex flex-col gap-1.5">
        <label v-if="label" class="text-secondary text-sm font-medium" :for="name">
            {{ label }} <span v-if="required" class="text-brand-tertiary">*</span>
        </label>
        <div :class="wrapperClasses">
            <component
                :is="icon"
                v-if="icon && iconPosition === 'leading'"
                class="absolute left-3 size-5 text-tertiary pointer-events-none"
            />
            <input
                :id="name"
                :class="[
                    baseClasses,
                    variantClasses[destructive ? 'destructive' : 'default'],
                    sizeClasses[size],
                    { 'pl-10': icon && iconPosition === 'leading' },
                    { 'pr-10': icon && iconPosition === 'trailing' },
                ]"
                :type="type"
                :placeholder="placeholder"
                :name="name"
                :value="modelValue"
                @input="$emit('update:modelValue', ($event.target as HTMLInputElement).value)"
            />
            <component
                :is="icon"
                v-if="icon && iconPosition === 'trailing'"
                class="absolute right-3 size-5 text-tertiary pointer-events-none"
            />
        </div>
        <p v-if="hintText" :class="['text-sm', destructive ? 'text-error-primary' : 'text-tertiary']">
            {{ hintText }}
        </p>
    </div>
</template>
