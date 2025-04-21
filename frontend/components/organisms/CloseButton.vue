<script setup lang="ts">
    interface CloseButtonProps {
        size?: 'sm' | 'md' | 'lg';
    }

    const _props = withDefaults(defineProps<CloseButtonProps>(), {
        size: 'sm',
    });

    const _emit = defineEmits<{
        click: [event: MouseEvent];
    }>();

    const baseClasses =
        'text-fg-quaternary rounded-lg hover:bg-primary-hover hover:text-fg-quaternary-hover focus:outline-none focus:ring focus:ring-primary transition';

    const sizeClasses = {
        sm: 'p-2',
        md: 'p-2',
        lg: 'p-2',
    };

    const iconSizeClasses = {
        sm: 'size-5',
        md: 'size-5',
        lg: 'size-6',
    };
</script>

<template>
    <button :class="[baseClasses, sizeClasses[size]]" @click="$emit('click', $event)">
        <XIcon :class="[iconSizeClasses[size]]" />
    </button>
</template>
