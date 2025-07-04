import type { UseFetchOptions } from 'nuxt/app';

export async function useAuthFetch<T>(url: string, options: UseFetchOptions<T> = {}) {
    await nextTick();

    const authStore = useAuthStore();
    const headers = useRequestHeaders(['cookie']);

    const finalOptions = (): UseFetchOptions<T> => {
        return {
            ...options,
            headers: {
                ...headers,
                ...(options.headers || {}),
            },
            credentials: 'include',
            immediate: true,
        };
    };

    let response = await useFetch(url, finalOptions());

    if (response.error.value?.statusCode === 401) {
        const refreshed = await authStore.refreshToken();

        if (refreshed) {
            response = await useFetch(url, finalOptions());
        } else {
            await authStore.logout();
            navigateTo('/login');
        }
    }

    return response;
}
