export default defineNuxtRouteMiddleware(() => {
    const authStore = useAuthStore();

    if (!authStore.user) {
        return navigateTo('/login');
    }

    const hasAccess = authStore.user.roles.includes('ROLE_OWNER') || authStore.user.roles.includes('ROLE_CLIENT');

    if (!hasAccess) {
        return navigateTo('/');
    }
});
