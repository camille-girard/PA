<script setup lang="ts">
    interface DividerProps {
        orientation?: 'vertical' | 'horizontal';
    }

    const _props = withDefaults(defineProps<DividerProps>(), {
        orientation: 'horizontal',
    });

    const orientationClasses = {
        vertical: 'w-px',
        horizontal: 'h-px',
    };
</script>

<template>
    <div :class="['bg-[var(--border-secondary)]', orientationClasses[orientation]]" />
</template>
