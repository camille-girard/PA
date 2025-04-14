<script lang="ts" setup>
const authStore = useAuthStore()
const router = useRouter()

async function handleLogout() {
    await authStore.logout()
    router.push('/login')
}
</script>

<template>
    <UButton :disabled="authStore.isLoading" @click="handleLogout">
      {{ authStore.isLoading ? 'Logging out...' : 'Logout' }}
    </UButton>
</template>