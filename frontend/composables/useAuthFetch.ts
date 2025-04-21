import type { UseFetchOptions } from 'nuxt/app';

export function useAuthFetch<T>(url: string, options: UseFetchOptions<T> = {}) {
    const { $api } = useNuxtApp();
    const headers = useRequestHeaders(['cookie']);

    return useFetch($api(url), {
        ...options,
        headers: {
            ...headers,
            ...(options.headers || {}),
        },
        credentials: 'include',
        watch: false,
        ...options,
    });
}
