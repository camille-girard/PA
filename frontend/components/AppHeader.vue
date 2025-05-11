<script setup lang="ts">
    const authStore = useAuthStore();
</script>

<template>
    <header class="h-20 w-full fixed z-10 top-0 flex items-center bg-primary shadow-md">
        <div class="max-w-7xl w-full mx-auto flex items-center justify-between h-fit">
            <div class="flex items-center gap-2">
                <ULogo />
                <span class="text-xl font-semibold text-primary">PopnBed</span>
            </div>
            <nav class="h-full">
                <ul class="h-full flex items-center gap-4">
                    <li>
                        <ULink to="#">Thématiques</ULink>
                    </li>
                    <li>
                        <ULink to="#">Explorer</ULink>
                    </li>
                </ul>
            </nav>
            <UButton v-if="!authStore.isAuthenticated" size="lg" @click="navigateTo('/login')">S'identifier </UButton>
            <LogoutButton v-else />
        </div>
    </header>
</template>

<style scoped></style>
