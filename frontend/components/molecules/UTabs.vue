<script setup lang="ts">
    interface TabItem {
        name: string;
        label: string;
    }

    interface TabsProps {
        items: TabItem[];
        variant?: 'brand' | 'gray' | 'underline' | 'border' | 'minimal';
        fullWidth?: boolean;
        size?: 'sm' | 'md';
        content?: boolean;
    }

    const _props = withDefaults(defineProps<TabsProps>(), {
        fullWidth: false,
        content: true,
        size: 'sm',
        variant: 'brand',
    });

    const defaultActiveTab = ref(0);
    const activeTab = ref<TabItem>(_props.items[defaultActiveTab.value]);

    const baseClasses = 'transition text-quaternary font-semibold';

    const containerClasses = {
        brand: {
            sm: 'gap-1',
            md: 'gap-1',
        },
        gray: {
            sm: 'gap-1',
            md: 'gap-1',
        },
        underline: {
            sm: 'gap-1 border-b border-secondary',
            md: 'gap-1 border-b border-secondary',
        },
        border: {
            sm: 'gap-1 p-1 border border-secondary bg-secondary-alt rounded-[10px]',
            md: 'p-1.5 gap-1',
        },
        minimal: {
            sm: 'gap-0.5 bg-secondary-alt border border-secondary rounded-lg overflow-hidden',
            md: '',
        },
    };

    const variantClasses = {
        brand: 'hover:text-brand-secondary hover:bg-brand-primary-alt',
        gray: 'hover:text-secondary hover:bg-active',
        underline: 'hover:border-b-2 hover:border-fg-brand-primary-alt hover:text-brand-secondary',
        border: 'hover:shadow-sm hover:bg-primary-alt hover:text-secondary',
        minimal: '',
    };

    const sizeClasses = {
        brand: {
            sm: 'px-3 py-2 gap-2 text-sm rounded-md',
            md: 'px-3 py-2 gap-2 rounded-md',
        },
        gray: {
            sm: 'px-3 py-2 gap-2 text-sm rounded-md',
            md: 'px-3 py-2 gap-2 rounded-md',
        },
        underline: {
            sm: 'pb-3 px-1 gap-1 text-sm',
            md: 'pb-3 px-1 gap-1',
        },
        border: {
            sm: 'px-3 py-2 gap-2 text-sm rounded-md',
            md: 'px-3 py-2 gap-2 rounded-md',
        },
        minimal: {
            sm: 'px-3 py-2 gap-2 rounded-lg text-sm',
            md: 'px-3 py-2 gap-2 rounded-lg',
        },
    };

    function activeClasses(variant: 'brand' | 'gray' | 'underline' | 'border' | 'minimal', isActive: boolean): string {
        const classes = {
            brand: '!text-brand-secondary bg-brand-primary-alt',
            gray: '!text-secondary bg-active',
            underline: 'border-b-2 !border-fg-brand-primary-alt !text-brand-secondary',
            border: 'bg-primary-alt !text-secondary shadow-sm',
            minimal: 'bg-primary-alt border border-primary shadow-xs !text-secondary',
        };

        if (isActive) {
            return classes[variant];
        } else return '';
    }

    function handleTabClick(item: TabItem, _index: number): void {
        activeTab.value = item;
    }
</script>

<template>
    <div :class="['flex items-center', { 'w-full': fullWidth, 'w-fit': !fullWidth }, containerClasses[variant][size]]">
        <button
            v-for="(item, index) in items"
            :key="index"
            :class="[
                baseClasses,
                variantClasses[variant],
                sizeClasses[variant][size],
                activeClasses(variant, activeTab.name === item.name),
                { 'w-full': fullWidth },
            ]"
            @click="handleTabClick(item, index)"
        >
            {{ item.label }}
        </button>
    </div>

    <div v-for="(item, index) in items" v-show="activeTab.label === item.label" :key="index">
        <slot :name="item.name" :item="item" />
    </div>
</template>
