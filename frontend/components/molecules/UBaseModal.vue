<script setup lang="ts">
    import UCirclesBackgroundPattern from '../atoms/patterns/UCirclesBackgroundPattern.vue';
    import UGridBackgroundPattern from '../atoms/patterns/UGridBackgroundPattern.vue';
    import USquaresBackgroundPattern from '../atoms/patterns/USquaresBackgroundPattern.vue';

    interface BaseModalProps {
        isOpen: boolean;
        closeButton?: boolean;
        background?: 'grid' | 'circles' | 'squares' | 'empty';
        backgroundSize?: 'sm' | 'md' | 'lg';
    }

    const _props = withDefaults(defineProps<BaseModalProps>(), {
        closeButton: true,
        background: 'empty',
        backgroundSize: 'sm',
    });

    const backgroundComponent = {
        empty: null,
        grid: UGridBackgroundPattern,
        circles: UCirclesBackgroundPattern,
        squares: USquaresBackgroundPattern,
    };

    const backgroundSizeClasses = {
        sm: 'size-80 absolute -top-16 -left-14',
        md: 'size-[480px]',
        lg: '',
    };

    const emit = defineEmits(['close']);

    function close() {
        emit('close');
    }
</script>

<template>
    <transition name="fade">
        <div v-if="isOpen" class="fixed inset-0 z-40 bg-black/50 backdrop-blur-sm" @click.self="close" />
    </transition>

    <transition name="modal">
        <div v-if="isOpen" class="fixed inset-0 z-50 flex items-center justify-center" @click.self="close">
            <div
                class="relative bg-primary rounded-2xl shadow-xl max-w-lg w-full transform transition-all overflow-hidden"
            >
                <component
                    :is="backgroundComponent[background]"
                    v-if="background !== 'empty'"
                    :class="[backgroundSizeClasses[backgroundSize], 'scale-150 -z-10']"
                />
                <CloseButton v-if="closeButton" class="absolute top-6 right-6" @click="close" />
                <slot />
            </div>
        </div>
    </transition>
</template>

<style scoped>
    .fade-enter-active,
    .fade-leave-active {
        transition: opacity 0.2s;
    }
    .fade-enter-from,
    .fade-leave-to {
        opacity: 0;
    }

    .modal-enter-active,
    .modal-leave-active {
        transition: all 0.2s ease;
    }

    .modal-enter-from {
        opacity: 0;
        transform: scale(0.95) translateY(10px);
    }

    .modal-leave-to {
        opacity: 0;
        transform: scale(0.95) translateY(10px);
    }
</style>
