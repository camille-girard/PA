import type { UseFetchOptions } from 'nuxt/app'

export async function useAuthFetch<T>(
  url: string,
  options: UseFetchOptions<T> = {}
) {
  const { $api } = useNuxtApp()
  const authStore = useAuthStore()
  
  // Ensure we have a valid token first
  // Need to use type assertion due to TypeScript limitations with Pinia
  await authStore.ensureValidToken()
  
  // If we're not authenticated after refresh attempt, throw an error
  if (!authStore.isAuthenticated) {
    throw new Error('Authentication required')
  }
  
  return useFetch($api(url), {
    ...options,
    headers: {
      ...(options.headers || {}),
      Authorization: `Bearer ${authStore.accessToken}`
    },
    watch: false
  })
}
