<script setup lang="ts">
    interface BadgeProps {
        size?: 'sm' | 'md' | 'lg';
        variant?: 'pill' | 'badge' | 'modern';
        color?: 'brand' | 'error' | 'warning' | 'success';
        icon?: 'false' | 'dot';
    }

    const _props = withDefaults(defineProps<BadgeProps>(), {
        size: 'md',
        variant: 'pill',
        color: 'brand',
        icon: 'false',
    });

    const baseClasses = 'flex items-center gap-1.5';

    const sizeClasses = {
        sm: 'px-2 py-0.5 text-xs',
        md: 'px-2.5 py-0.5 text-sm',
        lg: 'px-3 py-0.5 text-sm',
    };

    const variantClasses = {
        pill: '',
        badge: '',
        modern: 'shadow-xs !text-secondary !bg-primary !border-primary',
    };

    const roundedClasses = {
        sm: {
            pill: 'rounded-full',
            badge: 'rounded-md',
            modern: 'rounded-md',
        },
        md: {
            pill: 'rounded-full',
            badge: 'rounded-md',
            modern: 'rounded-md',
        },
        lg: {
            pill: 'rounded-full',
            badge: 'rounded-lg',
            modern: 'rounded-lg',
        },
    };

    const colorClasses = {
        brand: 'bg-utility-brand-50 border border-utility-brand-200 text-utility-brand-700',
        error: 'bg-utility-error-50 border border-utility-error-200 text-utility-error-700',
        warning: 'bg-utility-warning-50 border border-utility-warning-200 text-utility-warning-700',
        success: 'bg-utility-success-50 border border-utility-success-200 text-utility-success-700',
    };

    const dotClasses = {
        brand: 'bg-utility-brand-500',
        error: 'bg-utility-error-500',
        warning: 'bg-utility-warning-500',
        success: 'bg-utility-success-500',
    };
</script>

<template>
    <div
        :class="[
            baseClasses,
            sizeClasses[size],
            variantClasses[variant],
            colorClasses[color],
            roundedClasses[size][variant],
        ]"
    >
        <div v-if="icon === 'dot'" :class="['rounded-full m-px size-1.5', dotClasses[color]]" />
        <slot />
    </div>
</template>
