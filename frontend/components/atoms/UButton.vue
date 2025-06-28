<script setup lang="ts">
    interface ButtonProps {
        size?: 'sm' | 'md' | 'lg' | 'xl';
        variant?: 'primary' | 'secondary' | 'tertiary' | 'transparent';
        disabled?: boolean;
        isLoading?: boolean;
        icon?: Component | string | null;
        iconPosition?: 'leading' | 'trailing';
    }

    const _props = withDefaults(defineProps<ButtonProps>(), {
        size: 'md',
        variant: 'primary',
        disabled: false,
        isLoading: false,
        icon: null,
        iconPosition: 'leading',
    });

    const _emit = defineEmits<{
        (e: 'click', event: MouseEvent): void;
    }>();

    const baseClasses =
        'text-body-md flex items-center font-semibold gap-1 rounded-lg transition focus:ring-1 focus:ring-primary ring-offset-2 focus:outline-none ring-offset-transparent [&_svg]:size-5';

    const sizeClasses = {
        sm: 'px-3 py-2 text-sm',
        md: 'px-3.5 py-2.5 text-sm',
        lg: 'px-4 py-2.5',
        xl: 'px-4.5 py-3',
    };

    const variantClasses = {
        primary:
            'justify-center bg-brand-solid hover:bg-brand-solid-hover text-white enabled:shadow-xs-custom border border-brand enabled:bg-linear-grad disabled:border-disabled-subtle disabled:bg-disabled disabled:text-fg-disabled [&_svg]:text-button-primary-icon [&_svg]:hover:text-button-primary-icon-hover',
        secondary:
            'justify-center text-secondary hover:text-secondary-hover shadow-xs-skeuomorphic disabled:text-fg-disabled disabled:border-disabled-subtle [&_svg]:text-fg-quaternary [&_svg]:hover:text-fg-quaternary-hover',
        tertiary:
            'justify-center text-tertiary enabled:hover:bg-primary-hover hover:text-tertiary-hover disabled:text-fg-disabled',
        transparent:
            'justify-start bg-transparent text-gray-700 hover:text-orange-600 text-left px-4 py-2 w-full font-medium [&_svg]:text-gray-700 [&_svg]:hover:text-orange-600',
        danger: 'justify-center bg-red-100 hover:bg-red-200 text-red-700 border border-red-300 font-semibold shadow-sm disabled:opacity-50',
    };
</script>

<template>
    <button
        :class="[
            baseClasses,
            sizeClasses[size],
            variantClasses[variant],
            { 'opacity-50 cursor-not-allowed': disabled },
            { 'flex-row-reverse': iconPosition === 'trailing' },
        ]"
        :disabled="disabled"
        @click="$emit('click', $event)"
    >
        <component :is="icon" />
        <slot />
    </button>
</template>
