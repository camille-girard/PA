<script setup lang="ts">
import { ref, computed } from 'vue'
import { useAuthStore } from '~/stores/auth'

const authStore = useAuthStore();
const isMenuOpen = ref(false);
const isProfileMenuOpen = ref(false);

const canViewAccommodation = computed(() => {
  if (!authStore.isAuthenticated || !authStore.user?.roles) {
    return false
  }

  return authStore.user.roles.some(role =>
      role === 'ROLE_OWNER' || role === 'ROLE_ADMIN'
  )
})

const toggleProfileMenu = () => {
  isProfileMenuOpen.value = !isProfileMenuOpen.value
}

const closeProfileMenu = () => {
  isProfileMenuOpen.value = false;
};
</script>

<template>
  <header class="bg-white py-5 w-full fixed top-0 z-50 pt-8 px-4">
    <div class="max-w-7xl mx-auto w-full flex items-center justify-between">
      <NuxtLink to="/" class="flex items-center gap-2">
        <ULogo />
        <span class="text-body-sm font-semibold">PopnBed</span>
      </NuxtLink>

      <nav class="hidden md:flex items-center gap-6">
        <ULink to="/thematiques">Thématiques</ULink>
        <ULink to="/explorer">Explorer</ULink>
      </nav>

      <div class="hidden md:block relative">
        <UButton v-if="!authStore.isAuthenticated" size="lg" @click="navigateTo('/login')">
          Se connecter
        </UButton>

        <div v-else>
          <button class="rounded-full focus:outline-none" @click="toggleProfileMenu">
            <img src="/Patrick.jpg" alt="Avatar" class="w-12 h-12 rounded-full object-cover mb-4" />
          </button>

          <transition name="fade">
            <div
                v-if="isProfileMenuOpen"
                class="absolute right-0 mt-2 py-2 w-52 bg-white shadow-lg rounded-md"
                @click.away="closeProfileMenu"
            >
              <ULink to="/profile" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Mon Profil</ULink>
              <ULink to="/message" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Mes Messages</ULink>
              <ULink to="/booking" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Mes Réservations</ULink>

              <ULink
                  v-if="canViewAccommodation"
                  to="/my-accommodation"
                  class="block px-4 py-2 text-gray-700 hover:bg-gray-100"
              >
                Mon Hébergement
              </ULink>

              <ULink
                  v-if="!canViewAccommodation"
                  to="/owner-request"
                  class="block px-4 py-2 text-gray-700 hover:bg-gray-100"
              >
                Devenir propriétaire
              </ULink>

              <ULink
                  v-if="authStore.user?.roles?.includes('ROLE_OWNER')"
                  to="/booking/manage-booking"
                  class="block px-4 py-2 text-gray-700 hover:bg-gray-100"
              >
                Gestion de mes réservations
              </ULink>

              <div class="px-2 pt-2">
                <LogoutButton variant="transparent" />
              </div>
            </div>
          </transition>
        </div>
      </div>

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

        <template v-if="authStore.isAuthenticated">
          <ULink to="/profile" class="block text-gray-700 font-medium">Mon Profil</ULink>
          <ULink to="/message" class="block text-gray-700 font-medium">Mes Messages</ULink>
          <ULink to="/booking" class="block text-gray-700 font-medium">Mes Réservations</ULink>

          <ULink
              v-if="canViewAccommodation"
              to="/my-accommodation"
              class="block text-gray-700 font-medium"
          >
            Mon Hébergement
          </ULink>

          <ULink
              v-if="!canViewAccommodation"
              to="/owner-request"
              class="block text-gray-700 font-medium"
          >
            Devenir propriétaire
          </ULink>

          <ULink
              v-if="authStore.user?.roles?.includes('ROLE_OWNER')"
              to="/booking/manage-booking"
              class="block text-gray-700 font-medium"
          >
            Gestion de mes réservations
          </ULink>

          <LogoutButton variant="transparent" />
        </template>

        <div v-else>
          <UButton size="lg" class="w-full" @click="navigateTo('/login')">
            Se connecter
          </UButton>
        </div>
      </div>
    </transition>
  </header>
</template>