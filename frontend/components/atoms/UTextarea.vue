<script setup lang="ts">
    interface TextAreaProps {
        id?: string;
        modelValue?: string;
        label?: string;
        placeholder?: string;
        hintText?: string;
        destructive?: boolean;
        disabled?: boolean;
        type?: 'default' | 'tags';
        name?: string;
        required?: boolean;
        rows?: number;
    }

    const _props = withDefaults(defineProps<TextAreaProps>(), {
        id: '',
        label: '',
        placeholder: '',
        hintText: '',
        disabled: false,
        destructive: false,
        type: 'default',
        name: '',
        required: false,
        rows: 3,
        modelValue: '',
    });

    const _emit = defineEmits<{
        'update:modelValue': [value: string];
    }>();

    const wrapperClasses = 'flex items-center relative w-full';
    const baseClasses =
        'flex gap-2 border bg-primary rounded-lg focus:ring-2 focus:border-transparent focus:outline-none placeholder:text-placeholder text-primary shadow-xs w-full px-3.5 py-3';
</script>

<template>
    <div class="flex flex-col gap-1.5">
        <label v-if="label" class="text-secondary text-sm font-medium" :for="name">
            {{ label }} <span v-if="required" class="text-brand-tertiary">*</span>
        </label>
        <div :class="wrapperClasses">
            <textarea
                :id="id"
                :name="name"
                :rows="rows"
                :class="[baseClasses]"
                :required="required"
                :placeholder="placeholder"
                @change="$emit('update:modelValue', ($event.target as HTMLTextAreaElement).value)"
            />
        </div>
        <p v-if="hintText" :class="['text-sm', destructive ? 'text-error-primary' : 'text-tertiary']">
            {{ hintText }}
        </p>
    </div>
</template>
