<script setup lang="ts">
    interface Props {
        to: string;
        variant?: 'primary' | 'secondary';
        size?: 'sm' | 'md';
        disabled?: boolean;
        showExternalIcon?: boolean;
        className?: string;
    }

    const props = withDefaults(defineProps<Props>(), {
        variant: 'primary',
        size: 'md',
        disabled: false,
        showExternalIcon: true,
        className: '',
    });

    const _emit = defineEmits(['click']);

    const isExternal = computed<boolean>(() => {
        return props.to.startsWith('http') || props.to.startsWith('mailto:') || props.to.startsWith('tel:');
    });

    const baseClasses =
        'flex gap-1 items-center font-semibold focus:ring focus:ring-primary ring-offset-2 rounded ring-offset-transparent';

    const variantClasses = {
        primary:
            'text-brand-secondary hover:text-brand-secondary-hover hover:underline underline-offset-4 transition disabled:text-fg-disabled',
        secondary:
            'text-tertiary hover:text-tertiary-hover hover:underline underline-offset-4 transition disabled:text-fg-disabled',
    };

    const sizeClasses = {
        sm: 'text-sm',
        md: 'text-base',
    };
</script>

<template>
    <NuxtLink
        :to="to"
        :target="isExternal ? '_blank' : undefined"
        :rel="isExternal ? 'noopener noreferrer' : undefined"
        :class="[
            baseClasses,
            variantClasses[variant],
            sizeClasses[size],
            disabled ? 'opacity-50 cursor-not-allowed' : '',
            className,
        ]"
        @click="!disabled && $emit('click', $event)"
    >
        <slot />
        <svg
            v-if="isExternal ? showExternalIcon : false"
            class="size-4"
            xmlns="http://www.w3.org/2000/svg"
            width="24"
            height="24"
            viewBox="0 0 24 24"
            fill="none"
        >
            <path
                d="M21 9L21 3M21 3H15M21 3L13 11M10 5H7.8C6.11984 5 5.27976 5 4.63803 5.32698C4.07354 5.6146 3.6146 6.07354 3.32698 6.63803C3 7.27976 3 8.11984 3 9.8V16.2C3 17.8802 3 18.7202 3.32698 19.362C3.6146 19.9265 4.07354 20.3854 4.63803 20.673C5.27976 21 6.11984 21 7.8 21H14.2C15.8802 21 16.7202 21 17.362 20.673C17.9265 20.3854 18.3854 19.9265 18.673 19.362C19 18.7202 19 17.8802 19 16.2V14"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
            />
        </svg>
    </NuxtLink>
</template>
