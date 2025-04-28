<script lang="ts" setup>
    const authStore = useAuthStore();

    async function handleLogout() {
        await authStore.logout();
    }
</script>

<template>
    <UButton :disabled="authStore.isLoading" @click="handleLogout">
        {{ authStore.isLoading ? 'Logging out...' : 'Logout' }}
    </UButton>
</template>
