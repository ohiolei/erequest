import { computed, ref } from 'vue';

const pendingCount = ref(0);
const label = ref('Please wait...');

export function useLoading() {
    const isLoading = computed(() => pendingCount.value > 0);

    const startLoading = (message = 'Please wait...') => {
        label.value = message;
        pendingCount.value += 1;
    };

    const stopLoading = () => {
        pendingCount.value = Math.max(0, pendingCount.value - 1);
        if (pendingCount.value === 0) {
            label.value = 'Please wait...';
        }
    };

    const withLoading = async (callback, message = 'Please wait...') => {
        startLoading(message);
        try {
            return await callback();
        } finally {
            stopLoading();
        }
    };

    return {
        isLoading,
        label,
        startLoading,
        stopLoading,
        withLoading,
    };
}
