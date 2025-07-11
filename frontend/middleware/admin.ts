export default defineNuxtRouteMiddleware(async () => {
    const authStore = useAuthStore();

    if (authStore.isAuthenticated && !authStore.user) {
        await authStore.fetchUser();
    }

    if (!authStore.isAuthenticated) {
        return navigateTo('/login');
    }

    if (!authStore.isAdmin) {
        return navigateTo('/');
    }
});
