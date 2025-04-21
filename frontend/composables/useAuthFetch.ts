import type { UseFetchOptions } from 'nuxt/app';

export async function useAuthFetch<T>(url: string, options: UseFetchOptions<T> = {}) {
    const { $api } = useNuxtApp();
    const authStore = useAuthStore();
    const headers = useRequestHeaders(['cookie']);

    const finalOptions: UseFetchOptions<T> = {
        ...options,
        headers: {
            ...headers,
            ...(options.headers || {}),
        },
        credentials: 'include',
        watch: false,
    };

    let response = await useFetch($api(url), finalOptions);

    if (response.error.value?.statusCode === 401) {
        const refreshed = await authStore.refreshToken();

        if (refreshed) {
            response = await useFetch($api(url), finalOptions);
        } else {
            await authStore.logout();
            navigateTo('/login');
        }
    }

    return response;
}
