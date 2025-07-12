import type { Toast, ToastOptions } from '~/types/toast';

export const useToastStore = defineStore('toast', {
    state: () => ({
        toasts: [] as Toast[],
        maxToasts: 5,
    }),

    actions: {
        addToast(toast: ToastOptions) {
            const id = Date.now().toString();

            const newToast: Toast = {
                ...toast,
                id,
                title: toast.title || 'Notification',
                message: toast.message || 'Notification',
                type: toast.type || 'info',
                duration: toast.duration || 5000,
                closable: toast.closable !== undefined ? toast.closable : true,
                createdAt: Date.now(),
            };

            this.toasts.push(newToast);

            if (this.toasts.length > this.maxToasts) {
                this.toasts.shift();
            }

            if (newToast.duration && newToast.duration > 0) {
                setTimeout(() => {
                    this.removeToast(id);
                }, newToast.duration);
            }

            return id;
        },

        removeToast(id: string) {
            const index = this.toasts.findIndex((toast) => toast.id === id);
            if (index !== -1) {
                this.toasts.splice(index, 1);
            }
        },

        success(title: string, message: string, options = {}) {
            return this.addToast({ title, message, type: 'success', ...options });
        },

        error(title: string, message: string, options = {}) {
            return this.addToast({ title, message, type: 'error', ...options });
        },

        info(title: string, message: string, options = {}) {
            return this.addToast({ title, message, type: 'info', ...options });
        },

        warning(title: string, message: string, options = {}) {
            return this.addToast({ title, message, type: 'warning', ...options });
        },
    },
});
