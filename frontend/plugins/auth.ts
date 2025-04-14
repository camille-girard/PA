export default defineNuxtPlugin(async (nuxtApp) => {
    const authStore = useAuthStore()

    // Initial user fetch on client-side if we have tokens but no user data
    if (import.meta.client && authStore.isAuthenticated && !authStore.user) {
        await authStore.fetchUser()
    }

    // Add global fetch interceptor for token management
    nuxtApp.hook('app:created', () => {
        const originalFetch = globalThis.fetch
        
        globalThis.fetch = async function(...args) {
            const [resource, options = {}] = args
            
            // Only intercept API requests
            const config = useRuntimeConfig()
            const apiUrl = config.public.apiUrl
            
            if (typeof resource === 'string' && resource.startsWith(apiUrl) && authStore.isAuthenticated) {
                // Skip token refresh for refresh token endpoint to avoid infinite loops
                if (!resource.includes('/auth/refresh')) {
                    // Check if token needs refresh
                    if (authStore.isTokenExpired) {
                        const refreshSuccessful = await authStore.refreshAccessToken()
                        
                        // If refresh failed and we're not already on the login page, redirect
                        if (!refreshSuccessful && window.location.pathname !== '/login') {
                            navigateTo('/login')
                            throw new Error('Authentication required')
                        }
                    }
                }
                
                // Add Authorization header with token
                if (authStore.accessToken) {
                    options.headers = {
                        ...options.headers,
                        'Authorization': `Bearer ${authStore.accessToken}`
                    }
                }
            }
            
            return originalFetch(resource, options)
        }
    })
})