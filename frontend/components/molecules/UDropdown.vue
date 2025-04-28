<script setup lang="ts">
    import ChrevronDownIcon from '~/components/atoms/icons/ChrevronDownIcon.vue';

    interface MenuItem {
        label: string;
        icon: Component;
    }

    interface DropdownProps {
        label: string;
        menuItems: MenuItem[];
        position?: 'top-left' | 'top-right' | 'bottom-left' | 'bottom-right';
    }

    const props = withDefaults(defineProps<DropdownProps>(), {
        position: 'bottom-left',
    });

    const isOpen = ref(false);
    const dropdownPosition = ref<string>(props.position);

    function toggleDropdown() {
        isOpen.value = !isOpen.value;
    }

    onMounted(() => {
        document.addEventListener('click', (e: MouseEvent) => {
            const target = e.target as HTMLElement;
            if (target && !target.closest('.dropdown-container')) {
                isOpen.value = false;
            }
        });
    });
</script>

<template>
    <div class="dropdown-container relative inline-block">
        <UButton
            size="sm"
            :icon="ChrevronDownIcon"
            icon-position="trailing"
            variant="secondary"
            @click="toggleDropdown"
        >
            {{ label }}
        </UButton>

        <Transition :name="`dropdown-${dropdownPosition}`">
            <div
                v-if="isOpen"
                class="absolute w-64 rounded-lg shadow-lg bg-primary border border-secondary-alt z-10"
                :class="{
                    'top-auto bottom-full mb-2': dropdownPosition.startsWith('top'),
                    'top-full mt-2': dropdownPosition.startsWith('bottom'),
                    'left-0': dropdownPosition.endsWith('left'),
                    'right-0': dropdownPosition.endsWith('right'),
                }"
            >
                <div class="py-1">
                    <div v-for="(item, index) in menuItems" :key="index" class="px-1.5 py-px">
                        <div
                            class="px-3 py-2 flex items-center gap-3 group hover:bg-primary-hover transition-colors rounded-md"
                        >
                            <div
                                class="flex items-center gap-2 w-full text-secondary group-hover:text-secondary-hover [&_svg]:text-fg-quaternary [&_svg]:group-hover:text-fg-quaternary-hover [&_svg]:size-4 text-sm font-semibold"
                            >
                                <component :is="item.icon" />
                                {{ item.label }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </Transition>
    </div>
</template>

<style scoped>
    .dropdown-bottom-right-enter-active,
    .dropdown-bottom-right-leave-active,
    .dropdown-bottom-left-enter-active,
    .dropdown-bottom-left-leave-active {
        transition:
            opacity 0.2s ease,
            transform 0.2s ease;
    }

    .dropdown-bottom-right-enter-from,
    .dropdown-bottom-right-leave-to,
    .dropdown-bottom-left-enter-from,
    .dropdown-bottom-left-leave-to {
        opacity: 0;
        transform: translateY(-10px);
    }

    .dropdown-top-right-enter-active,
    .dropdown-top-right-leave-active,
    .dropdown-top-left-enter-active,
    .dropdown-top-left-leave-active {
        transition:
            opacity 0.2s ease,
            transform 0.2s ease;
    }

    .dropdown-top-right-enter-from,
    .dropdown-top-right-leave-to,
    .dropdown-top-left-enter-from,
    .dropdown-top-left-leave-to {
        opacity: 0;
        transform: translateY(10px);
    }
</style>
