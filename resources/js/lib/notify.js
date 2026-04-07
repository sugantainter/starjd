import { ref } from 'vue';

const notifications = ref([]);

export function useNotify() {
    function show(message, type = 'success', duration = 4000) {
        const id = Date.now();
        notifications.value.push({ id, message, type });
        setTimeout(() => {
            remove(id);
        }, duration);
    }

    function remove(id) {
        notifications.value = notifications.value.filter(n => n.id !== id);
    }

    return { notifications, show, remove };
}

// Global instance for non-setup usage if needed, or just export the ref
export const globalNotifications = notifications;
import { getErrorMessage } from './errorHandler.js';

export const notify = {
    success: (msg) => useNotify().show(msg, 'success'),
    error: (msg) => useNotify().show(getErrorMessage(msg), 'error'),
    info: (msg) => useNotify().show(msg, 'info'),
};
