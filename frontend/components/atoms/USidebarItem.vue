<script setup lang="ts">
    interface SidebarItemProps {
        title: string;
        icon?: Component | null;
        to?: string;
        collapsible?: boolean;
        defaultOpen?: boolean;
    }

    const props = withDefaults(defineProps<SidebarItemProps>(), {
        icon: null,
        to: '',
        collapsible: false,
        defaultOpen: false,
    });

    const route = useRoute();
    const isActive = computed(() => props.to === route.path);
    const isOpen = ref(props.defaultOpen);

    const handleClick = () => {
        if (props.collapsible) {
            isOpen.value = !isOpen.value;
        } else {
            navigateTo(props.to)
        }
    };

    const baseClasses =
        'group py-2.5 px-3 rounded-md bg-primary flex items-center gap-2 hover:bg-primary-hover transition duration-100 cursor-pointer';

    const contentEl = ref(null);

    const startTransition = (element: any) => {
        const height = element.scrollHeight;
        element.style.maxHeight = '0px';
        void element.offsetHeight;
        element.style.maxHeight = `${height}px`;
    };

    const endTransition = (element: any) => {
        const height = element.scrollHeight;
        element.style.maxHeight = `${height}px`;
        void element.offsetHeight;
        element.style.maxHeight = '0px';
    };
</script>

<template>
    <div>
        <component
            :is="to && !collapsible ? 'NuxtLink' : 'div'"
            :to="to && !collapsible ? to : undefined"
            :class="[baseClasses, { '!bg-active hover:!bg-secondary-hover': isActive }]"
            @click="handleClick"
        >
            <component :is="icon" v-if="icon" class="size-5 text-fg-quaternary" />
            <p
                class="text-secondary group-hover:text-secondary-hover font-semibold transition duration-100 ease-in-out flex-1"
            >
                {{ title }}
            </p>
            <ChrevronDownIcon
                v-if="collapsible"
                :class="[{ 'rotate-180': isOpen }, 'text-fg-quaternary transition-all ease-in-out duration-200']"
            />
        </component>

        <Transition name="collapse" @enter="startTransition" @leave="endTransition">
            <div v-if="collapsible && isOpen" ref="contentEl" class="overflow-hidden pl-5">
                <slot></slot>
            </div>
        </Transition>
    </div>
</template>

<style scoped>
    .collapse-enter-active,
    .collapse-leave-active {
        transition:
            max-height 0.3s ease,
            opacity 0.3s ease,
            transform 0.3s ease;
        overflow: hidden;
    }

    .collapse-enter-from,
    .collapse-leave-to {
        max-height: 0 !important;
        opacity: 0;
        transform: translateY(-10px);
    }
</style>

