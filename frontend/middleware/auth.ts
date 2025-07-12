export default defineNuxtRouteMiddleware(async (to) => {
    const authStore = useAuthStore();

    if (authStore.isAuthenticated && !authStore.user) {
        await authStore.fetchUser();
    }

    if (!authStore.isAuthenticated && to.path !== '/login') {
        return navigateTo('/login');
    }

    if (authStore.isAuthenticated && to.path === '/login') {
        return navigateTo('/');
    }
});
