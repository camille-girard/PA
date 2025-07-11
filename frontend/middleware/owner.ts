export default defineNuxtRouteMiddleware((_to, _from) => {
    const authStore = useAuthStore();

    if (!authStore.user) {
        return navigateTo('/login');
    }

    if (!authStore.user.roles.includes('ROLE_OWNER')) {
        return navigateTo('/');
    }
});
