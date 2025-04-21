import { useToastStore } from '~/stores/toast';

export function useToast() {
    const toastStore = useToastStore();

    return {
        success: (title: string, message: string, options = {}) => toastStore.success(title, message, options),
        error: (title: string, message: string, options = {}) => toastStore.error(title, message, options),
        warning: (title: string, message: string, options = {}) => toastStore.warning(title, message, options),
        info: (title: string, message: string, options = {}) => toastStore.info(title, message, options),

        remove: (id: string) => toastStore.removeToast(id),

        // Get all current toasts
        getToasts: () => toastStore.toasts,
    };
}
