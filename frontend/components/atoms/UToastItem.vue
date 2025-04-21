<script setup lang="ts">
    import type { Toast } from '~/types/toast';
    import AlertCircleIcon from './icons/AlertCircleIcon.vue';
    import CheckCircleIcon from './icons/CheckCircleIcon.vue';
    import InfoCircleIcon from './icons/InfoCircleIcon.vue';

    interface ToastItemProps {
        toast: Toast;
    }

    const _props = defineProps<ToastItemProps>();
    const toastStore = useToastStore();
    const showing = ref(false);

    onMounted(() => {
        setTimeout(() => {
            showing.value = true;
        }, 10); // Je met un petit delay pour que l'animation d'entrée s'effectuée correctement
    });

    const iconByType = {
        success: CheckCircleIcon,
        error: AlertCircleIcon,
        warning: AlertCircleIcon,
        info: InfoCircleIcon,
    };

    const baseClasses = 'flex p-4 gap-4 shadow-lg rounded-lg border border-secondary-alt relative max-w-[400px]';

    const iconColor = computed(() => {
        if (_props.toast.type === 'info') return 'brand';

        return _props.toast.type;
    });

    function dismiss() {
        showing.value = false;

        setTimeout(() => {
            toastStore.removeToast(_props.toast.id);
        }, 300);
    }
</script>

<template>
    <div :class="[baseClasses, showing ? 'translate-x-0 opacity-100' : 'translate-x-full opacity-0']">
        <UFeaturedIcon size="md" :icon="iconByType[toast.type]" :color="iconColor" />
        <div class="flex-grow space-y-1 pr-8">
            <p class="text-sm font-semibold text-fg-primary">{{ toast.title }}</p>
            <p class="text-sm text-fg-secondary">{{ toast.message }}</p>
        </div>
        <CloseButton size="sm" class="absolute top-2 right-2" @click="dismiss" />
    </div>
</template>
