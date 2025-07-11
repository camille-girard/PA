export type ToastType = 'success' | 'error' | 'info' | 'warning';
export type ToastPosition = 'top-right' | 'top-left' | 'bottom-right' | 'bottom-left' | 'top-center' | 'bottom-center';

export interface ToastOptions {
    title: string;
    message: string;
    type: ToastType;
    duration?: number;
    closable?: boolean;
    position?: ToastPosition;
    onClose?: () => void;
}

export interface Toast extends ToastOptions {
    id: string;
    createdAt: number;
}
