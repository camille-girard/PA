export default defineNuxtPlugin(async () => {
    const authStore = useAuthStore();

    if (!authStore.user && import.meta.client && authStore.isAuthenticated) {
        await authStore.fetchUser();
    }
});
