<script setup lang="ts">
import BreadcrumbElement from '~/components/BreadcrumbElement.vue';
import XIcon from '~/components/atoms/icons/XIcon.vue';
import MenuIcon from '~/components/atoms/icons/MenuIcon.vue';
import ULogo from '~/components/atoms/ULogo.vue';
import ULink from '~/components/atoms/ULink.vue';
import HomeIcon from '~/components/atoms/icons/HomeIcon.vue';
import UserIcon from '~/components/atoms/icons/UserIcon.vue';
import BuildingIcon from '~/components/atoms/icons/BuildingIcon.vue';
import CoinsHandIcon from '~/components/atoms/icons/CoinsHandIcon.vue';
import PinIcon from '~/components/atoms/icons/PinIcon.vue';
import LightningIcon from '~/components/atoms/icons/LightningIcon.vue';
import TicketIcon from '~/components/atoms/icons/TicketIcon.vue';
import MessageIcon from '~/components/atoms/icons/MessageIcon.vue';
import LogoutIcon from '~/components/atoms/icons/LogoutIcon.vue';
import { useRoute } from 'vue-router';

const authStore = useAuthStore();
const user = computed(() => authStore.user);

const router = useRouter();

const route = useRoute();

const isSidebarOpen = ref(false);

function isActiveNavItem(itemPath: string) {
    if (itemPath === '/backoffice') {
        return route.path === '/backoffice' || route.path === '/backoffice/';
    }
    return route.path.startsWith(itemPath);
}

const logout = async () => {
    await authStore.logout();
    router.push('/login');
};

const goToProfile = () => {
    router.push('/backoffice/profile');
};

const formattedRole = computed(() => {
    if (!user.value?.roles?.length) return 'User';
    return user.value.roles[0]
        .replace('ROLE_', '')
        .toLowerCase()
        .replace(/^\w/, (c) => c.toUpperCase());
});

function toggleSidebar() {
    isSidebarOpen.value = !isSidebarOpen.value;
}

function closeSidebar() {
    isSidebarOpen.value = false;
}

const navItems = [
    { to: '/backoffice', icon: HomeIcon, label: 'Tableau de bord' },
    { to: '/backoffice/owners', icon: BuildingIcon, label: 'Hôtes' },
    { to: '/backoffice/clients', icon: UserIcon, label: 'Clients' },
    { to: '/backoffice/admins', icon: BuildingIcon, label: 'Administrateurs' },
    { to: '/backoffice/bookings', icon: CoinsHandIcon, label: 'Réservations' },
    { to: '/backoffice/themes', icon: LightningIcon, label: 'Thèmes' },
    { to: '/backoffice/accommodations', icon: PinIcon, label: 'Logements' },
    { to: '/backoffice/tickets', icon: TicketIcon, label: 'Tickets' },
    { to: '/backoffice/owner-requests', icon: MessageIcon, label: 'Demandes Propriétaires' },
];


</script>

<template>
    <NuxtLayout name="default">
        <div class="flex h-full w-full overflow-hidden">
            <!-- Backdrop mobile -->
            <div
                v-if="isSidebarOpen"
                class="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm z-40 md:hidden"
                @click="closeSidebar"
            />

            <!-- Sidebar -->
            <aside
                :class="[
          'fixed top-0 bottom-0 left-0 z-50 bg-white shadow-lg transition-all duration-300 flex flex-col justify-between border-r',
          isSidebarOpen ? 'w-80' : 'w-16',
          'md:w-80 md:translate-x-0'
        ]"
            >
                <!-- Top Logo + Burger -->
                <div class="flex flex-col items-center pt-4 pb-4 px-3 space-y-3">
                    <button
                        class="md:hidden p-2 rounded hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-brand-500"
                        @click="toggleSidebar"
                    >
                        <component :is="isSidebarOpen ? XIcon : MenuIcon" class="w-6 h-6" />
                    </button>

                    <NuxtLink to="/" class=" pb-4 flex items-center justify-center space-x-2">
                        <ULogo class="w-10 h-10" />
                        <span class="hidden md:inline text-xl font-semibold">PopnBed</span>
                        <span v-if="isSidebarOpen" class="inline md:hidden text-xl font-semibold">PopnBed</span>
                    </NuxtLink>
                </div>

                <!-- Navigation -->
                <nav class="flex-1 overflow-y-auto">
                    <ul class="space-y-1 p-2 md:px-6">
                        <li v-for="item in navItems" :key="item.to">
                            <ULink
                                :to="item.to"
                                variant="tertiary"
                                :class="[
    'flex items-center px-3 py-3 rounded-lg hover:no-underline hover:bg-brand-600 hover:text-white focus:outline-none focus:ring-0',
    isActiveNavItem(item.to) ? 'bg-brand-600 text-white' : ''
  ]"
                            >
                                <component :is="item.icon" class="w-6 h-6" />
                                <span :class="[isSidebarOpen ? 'inline' : 'hidden', 'md:inline ml-3']">
    {{ item.label }}
  </span>
                            </ULink>

                        </li>
                    </ul>
                </nav>

                <!-- Bottom User -->
                <div class="border-t px-2 md:px-6 py-3 flex flex-col items-center md:items-stretch space-y-2">
                    <div
                        class="flex items-center w-full gap-3 cursor-pointer hover:bg-gray-50 px-3 py-2 rounded-lg"
                        @click="goToProfile"
                    >
                        <img
                            :src="user?.avatar || '/default-avatar.jpg'"
                            alt="Avatar"
                            class="w-10 h-10 rounded-full object-cover"
                        />
                        <div :class="[isSidebarOpen ? 'block' : 'hidden', 'md:block']">
                            <div class="font-semibold">{{ user?.firstName }} {{ user?.lastName }}</div>
                            <div class="text-xs text-gray-500">{{ formattedRole }}</div>
                        </div>
                    </div>

                    <button
                        class="flex items-center w-full px-3 py-3 rounded-lg hover:bg-brand-600 hover:text-white focus:outline-none text-left"
                        @click="logout"
                    >
                        <LogoutIcon class="w-6 h-6" />
                        <span :class="[isSidebarOpen ? 'inline' : 'hidden', 'md:inline ml-3']">
              Déconnexion
            </span>
                    </button>
                </div>
            </aside>

            <!-- Content -->
            <div :class="['flex-1 overflow-x-hidden transition-all duration-300', isSidebarOpen ? 'ml-64' : 'ml-16', 'md:ml-80']">
                <main class="py-6 px-4 md:px-8 flex flex-col gap-10">
                    <BreadcrumbElement />
                    <slot />
                </main>
            </div>
        </div>
    </NuxtLayout>
</template>

