<script setup lang="ts">
    import type { Component } from 'vue';
    import { provide, ref, watch } from 'vue';
    import URadioGroupItem from './URadioGroupItem.vue';

    interface RadioGroupItem {
        label: string;
        value: string;
        description: string;
        icon?: Component;
    }

    interface RadioGroupProps {
        items: RadioGroupItem[];
        modelValue?: string;
        disabled?: boolean;
        name?: string;
        orientation?: 'vertical' | 'horizontal';
    }

    const props = defineProps<RadioGroupProps>();

    const emit = defineEmits<{
        'update:modelValue': [value: string];
    }>();

    const selectedValue = ref(props.modelValue || '');

    watch(
        () => props.modelValue,
        (newValue) => {
            if (newValue !== undefined) {
                selectedValue.value = newValue;
            }
        }
    );

    watch(selectedValue, (value) => {
        emit('update:modelValue', value);
    });

    provide('radioGroupDisabled', props.disabled || false);
</script>

<template>
    <div :class="['w-full flex items-stretch gap-2', { 'flex-col': orientation === 'vertical' }]">
        <URadioGroupItem
            v-for="item in items"
            :key="item.value"
            :label="item.label"
            :value="item.value"
            :description="item.description"
            :icon="item.icon"
            :disabled="disabled"
            :group-value="selectedValue"
            @update:group-value="selectedValue = $event"
        />
    </div>
</template>
