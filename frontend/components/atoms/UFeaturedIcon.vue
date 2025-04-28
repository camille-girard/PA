<script setup lang="ts">
    interface FeaturedIconProps {
        size?: 'sm' | 'md' | 'lg' | 'xl';
        color?: 'brand' | 'gray' | 'error' | 'warning' | 'success';
        icon: Component;
    }

    const _props = withDefaults(defineProps<FeaturedIconProps>(), {
        size: 'sm',
        color: 'brand',
    });

    const baseClasses = 'rounded-full flex items-center justify-center';

    const colorClasses = {
        brand: 'bg-brand-secondary text-featured-icon-light-brand',
        gray: 'bg-tertiary text-featured-icon-light-gray',
        error: 'bg-error-secondary text-featured-icon-light-error',
        warning: 'bg-warning-secondary text-featured-icon-light-warning',
        success: 'bg-success-secondary text-featured-icon-light-success',
    };

    const sizeClasses = {
        sm: 'size-8',
        md: 'size-10',
        lg: 'size-12',
        xl: 'size-14',
    };

    const iconSizeClasses = {
        sm: 'size-4',
        md: 'size-5',
        lg: 'size-6',
        xl: 'size-7',
    };
</script>

<template>
    <div :class="[baseClasses, sizeClasses[size], colorClasses[color]]">
        <component :is="icon" :class="iconSizeClasses[size]" />
    </div>
</template>
