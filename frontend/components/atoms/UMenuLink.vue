<script setup lang="ts">
    import type { Component } from 'vue';

    export interface MenuLinkItem {
        icon: Component;
        label: string;
        action?: () => void;
    }

    interface MenuLinkProps {
        item: MenuLinkItem;
        variant?: 'primary' | 'secondary';
    }

    withDefaults(defineProps<MenuLinkProps>(), {
        variant: 'primary',
    });

    const getBgHoverClass = (variant: string) => {
        return variant === 'primary' ? 'hover:bg-primary-hover' : 'hover:bg-secondary-hover';
    };
</script>

<template>
    <div
        class="group p-2 flex items-center gap-2 rounded-md transition duration-100 ease-in-out cursor-pointer"
        :class="getBgHoverClass(variant)"
        @click="item.action && item.action()"
    >
        <component :is="item.icon" class="size-5 text-fg-quaternary" />
        <p class="text-secondary group-hover:text-secondary-hover flex-grow font-semibold text-sm">
            {{ item.label }}
        </p>
    </div>
</template>

