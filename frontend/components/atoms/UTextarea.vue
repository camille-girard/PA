<script setup lang="ts">
    interface TextareaProps {
        label?: string;
        name?: string;
        placeholder?: string;
        required?: boolean;
        hintText?: string;
        destructive?: boolean;
        modelValue?: string;
        rows?: number;
    }

    const _props = withDefaults(defineProps<TextareaProps>(), {
        label: '',
        name: '',
        placeholder: '',
        required: false,
        hintText: '',
        destructive: false,
        modelValue: '',
        rows: 4,
    });

    const _emit = defineEmits<{
        'update:modelValue': [value: string];
    }>();

const baseClasses =
    'w-full border rounded-lg shadow-xs text-sm text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 px-3 py-2';

    const variantClasses = {
        default: 'border-gray-300 focus:border-orange-500 focus:ring-orange-500',
        destructive: 'border-error-subtle focus:ring-error focus:border-error-subtle',
    };
</script>

<template>
    <div class="flex flex-col gap-1.5">
        <label v-if="label" class="text-body-sm" :for="name">
            {{ label }} <span v-if="required" class="text-brand-tertiary">*</span>
        </label>
        <textarea
            :id="name"
            :name="name"
            :rows="rows"
            :placeholder="placeholder"
            :class="[baseClasses, variantClasses[destructive ? 'destructive' : 'default']]"
            :value="modelValue"
            @input="$emit('update:modelValue', ($event.target as HTMLTextAreaElement).value)"
        />
        <p v-if="hintText" :class="['text-body-sm', destructive ? 'text-error-primary' : 'text-body-md']">
            {{ hintText }}
        </p>
    </div>
</template>
