import type { UseFetchOptions } from 'nuxt/app';

let isRefreshing = false;
let refreshPromise: Promise<boolean> | null = null;

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

    // Gestion des erreurs 401 uniquement si ce n'est pas déjà une requête de refresh
    if (response.error.value?.statusCode === 401 && !url.includes('/token/refresh')) {
        console.log('Received 401, attempting token refresh...');

        // Éviter les refreshs multiples simultanés
        if (isRefreshing) {
            if (refreshPromise) {
                const refreshSuccessful = await refreshPromise;
                if (refreshSuccessful) {
                    // Réessayer la requête après le refresh
                    console.log('Using existing refresh, retrying request...');
                    response = await useFetch(url, finalOptions());
                } else {
                    console.log('Existing refresh failed, user will be logged out');
                }
            }
        } else {
            isRefreshing = true;
            refreshPromise = authStore.refreshToken();

            try {
                const refreshSuccessful = await refreshPromise;

                if (refreshSuccessful) {
                    console.log('Token refresh successful, retrying original request...');
                    // Réessayer la requête originale avec un nouveau fetch
                    response = await useFetch(url, {
                        ...finalOptions(),
                        key: `${url}-retry-${Date.now()}`, // Force un nouveau fetch
                    });
                } else {
                    console.log('Token refresh failed, logging out...');
                    // Le refresh a échoué, déconnecter l'utilisateur
                    await authStore.logout();
                }
            } catch (error) {
                console.error('Error during token refresh:', error);
                await authStore.logout();
            } finally {
                isRefreshing = false;
                refreshPromise = null;
            }
        }
    }

    return response;
}
