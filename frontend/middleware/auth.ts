export default defineNuxtRouteMiddleware(async (to) => {
    const authStore = useAuthStore();

    // If we're authenticated but missing user data, fetch the user
    if (authStore.isAuthenticated && !authStore.user) {
        // This will also handle token refresh if needed
        await authStore.fetchUser();
    }

    // If not authenticated after checking/refreshing token, redirect to login
    if (!authStore.isAuthenticated && to.path !== '/login') {
        return navigateTo('/login');
    }

    // If authenticated and trying to access login page, redirect to home
    if (authStore.isAuthenticated && to.path === '/login') {
        return navigateTo('/');
    }
});
