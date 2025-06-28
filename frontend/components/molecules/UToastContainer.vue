<script setup lang="ts">
    interface ToastContainerProps {
        position?: 'top-right' | 'top-left' | 'bottom-left' | 'bottom-right';
    }

    const props = defineProps<ToastContainerProps>();

    const toastStore = useToastStore();
    const toasts = computed(() => toastStore.toasts);

    // Position classes based on the position prop
    const positionClasses = computed(() => {
        switch (props.position) {
            case 'top-left':
                return 'top-0 left-0';
            case 'top-right':
                return 'top-0 right-0';
            case 'bottom-left':
                return 'bottom-0 left-0';
            case 'bottom-right':
                return 'bottom-0 right-0';
            default:
                return 'top-0 right-0';
        }
    });
</script>

<template>
    <div v-if="toasts" class="fixed z-50 p-4 pointer-events-none" :class="positionClasses">
        <TransitionGroup name="toast" tag="div" class="flex flex-col items-end space-y-2">
            <UToastItem v-for="toast in toasts" :key="toast.id" :toast="toast" class="pointer-events-auto" />
        </TransitionGroup>
    </div>
</template>

<style scoped>
    .toast-enter-active,
    .toast-leave-active {
        transition: all 0.3s ease;
    }

    .toast-enter-from {
        opacity: 0;
        transform: translateX(100%);
    }

    .toast-leave-to {
        opacity: 0;
        transform: translateX(100%);
    }
</style>
