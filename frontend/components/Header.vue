<script setup lang="ts">
const authStore = useAuthStore();
</script>

<template>
  <header class="bg-white py-5 w-full fixed top-0 flex items-center z-50">
    <div class="max-w-7xl w-full mx-auto flex items-center justify-between h-fit">
      <div>
        <a href="/" class="flex items-center gap-2">
          <ULogo />
          <span class="text-xl font-semibold">PopnBed</span>
        </a>
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
      <UButton v-if="!authStore.isAuthenticated" size="lg" @click="navigateTo('/login')">Se connecter</UButton>
      <LogoutButton v-else />
    </div>
  </header>
</template>