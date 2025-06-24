<script lang="ts" setup>
    import LogoutIcon from '~/components/atoms/icons/LogoutIcon.vue';
    const authStore = useAuthStore();

    async function handleLogout() {
        await authStore.logout();
    }
</script>

<template>
    <UButton
        :disabled="authStore.isLoading"
        @click="handleLogout"
        variant="transparent"
        :icon="LogoutIcon"
        icon-position="leading"
    >
        {{ authStore.isLoading ? 'Logging out...' : 'Logout' }}
    </UButton>
</template>
