<script setup lang="ts">
import AllIcons from "~/components/AllIcons.vue";

const authStore = useAuthStore()
</script>

<template>
  <div class="flex min-h-screen bg-gray-25  text-gray-800">
    <!-- Sidebar -->
    <aside class="w-80 bg-white border-r shadow-sm flex flex-col justify-between min-h-screen fixed left-0 top-0 bottom-0">
      <div>
        <!-- Logo -->
        <div class="py-10 flex items-center justify-center space-x-2">
          <ULogo class="w-10 h-10" />
          <span class="font-semibold text-2xl">PopnBed</span>
        </div>

        <!-- Navigation -->
        <nav class="px-7 mt-4">

          <ul class="space-y-3">
            <ULink to="/backoffice"
                class="flex px-3 py-3 rounded-lg hover:bg-brand-600 hover:text-white hover:no-underline"
                exact-active-class="bg-brand-600 text-white">
              <HomeIcon class="w-7 h-7 mr-3" />
              Tableau de bord
            </ULink>

            <li>
              <ULink to="/backoffice/users"
                     class="block px-3 py-3 rounded-lg hover:bg-brand-600 hover:text-white hover:no-underline"
                     exact-active-class="bg-brand-600 text-white">
                <BuildingIcon class="w-7 h-7 mr-3" />
                Hôtes
              </ULink>
            </li>

            <li>
              <ULink to="/backoffice/rooms"
                     class="block px-3 py-3 rounded-lg hover:bg-brand-600 hover:text-white hover:no-underline"
                     exact-active-class="bg-brand-600 text-white">
                <UserIcon class="w-7 h-7 mr-3"/>
                Clients
              </ULink>
            </li>

            <li>
              <ULink to="/backoffice/bookings"
                     class="block px-3 py-3 rounded-lg hover:bg-brand-600 hover:text-white hover:no-underline"
                     exact-active-class="bg-brand-600 text-white">
                <CoinsHandIcon class="w-7 h-7 mr-3" />
                Réservations
              </ULink>
            </li>

            <li>
              <ULink to="/backoffice/bookings"
                     class="block px-3 py-3 rounded-lg hover:bg-brand-600 hover:text-white hover:no-underline"
                     exact-active-class="bg-brand-600 text-white">
                <MessageIcon class="w-7 h-7 mr-3" />
                Tickets
              </ULink>
            </li>

          </ul>
        </nav>
      </div>

      <!-- Footer -->
      <div class="px-10 py-20 flex items-center gap-3">
        <img src="/Patrick.jpg" alt="Avatar" class="w-10 h-10 rounded-full object-cover" />
        <div class="text-sm">
          <div class="font-semibold">caty</div>
          <div class="text-xs text-gray-500">Admin</div>
        </div>
      </div>
    </aside>

    <!-- Main Content Area -->
    <div class="flex-1 ml-64">
      <!-- Main slot -->
      <main class="p-6">
        <AllIcons />
        <slot />
      </main>
    </div>
  </div>

</template>
