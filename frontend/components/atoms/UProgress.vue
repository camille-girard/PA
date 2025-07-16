<script setup lang="ts">
    interface ProgressProps {
        value?: number;
        max?: number;
        height?: string;
    }

    const props = withDefaults(defineProps<ProgressProps>(), {
        value: 0,
        max: 100,
        height: '8px',
    });

    const progressPercentage = computed(() => {
        return Math.min(100, Math.max(0, (props.value / props.max) * 100));
    });
</script>

<template>
    <div class="w-full bg-quaternary rounded-full overflow-hidden" :style="`height: ${height};`">
        <div
            class="bg-brand-solid h-full rounded-full transition-all duration-300 ease-in-out"
            :style="`width: ${progressPercentage}%`"
        />
    </div>
</template>
