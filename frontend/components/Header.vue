<script setup lang="ts">
const authStore = useAuthStore();
const isMenuOpen = ref(false);
</script>

<template>
  <header class="bg-white py-5 w-full fixed top-0 z-50 pt-8 px-4">
    <div class="max-w-7xl mx-auto w-full flex items-center justify-between">
      <a href="/" class="flex items-center gap-2">
        <ULogo />
        <span class="text-body-sm font-semibold">PopnBed</span>
      </a>

      <nav class="hidden md:flex items-center gap-6">
        <ULink to="/thematiques">Thématiques</ULink>
        <ULink to="/explorer">Explorer</ULink>
      </nav>

      <div class="hidden md:block">
        <UButton
            v-if="!authStore.isAuthenticated"
            size="lg"
            @click="navigateTo('/login')"
        >
          Se connecter
        </UButton>
        <LogoutButton v-else />
      </div>

      <!-- Burger menu for mobile -->
      <button class="md:hidden" @click="isMenuOpen = !isMenuOpen">
        <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
        </svg>
      </button>
    </div>
    <transition name="fade">
      <div v-if="isMenuOpen" class="md:hidden mt-4 px-4 space-y-4 pt-6">
        <ULink to="/thematiques" class="block text-gray-700 font-medium">Thématiques</ULink>
        <ULink to="/explorer" class="block text-gray-700 font-medium">Explorer</ULink>
        <div>
          <UButton
              v-if="!authStore.isAuthenticated"
              size="lg"
              class="w-full"
              @click="navigateTo('/login')"
          >
            Se connecter
          </UButton>
          <LogoutButton v-else />
        </div>
      </div>
    </transition>
  </header>
</template>
