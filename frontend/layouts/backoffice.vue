<script setup lang="ts">
    import BreadcrumbElement from '~/components/BreadcrumbElement.vue';

    const authStore = useAuthStore();
    const user = computed(() => authStore.user);

    const router = useRouter();

    const logout = async () => {
        await authStore.logout();
        router.push('/login');
    };

    const goToProfile = () => {
        router.push('/backoffice/profile');
    };

    const formattedRole = computed(() => {
        if (!user.value?.roles?.length) return 'User';
        // Show first role nicely
        return user.value.roles[0]
            .replace('ROLE_', '')
            .toLowerCase()
            .replace(/^\w/, (c) => c.toUpperCase());
    });
</script>

<template>
    <NuxtLayout name="default">
        <div class="flex h-full w-full bg-gray-25 text-gray-800 overflow-x-hidden">
            <!-- Sidebar -->
            <aside
                class="w-80 bg-white border-r shadow-sm flex flex-col justify-between h-full fixed left-0 top-0 bottom-0"
            >
                <div>
                    <NuxtLink to="/" class="pt-6 pb-4 flex items-center justify-center space-x-2">
                        <ULogo class="w-10 h-10" />
                        <span class="text-2xl font-semibold">PopnBed</span>
                    </NuxtLink>

                    <nav class="px-7 mt-4">
                        <ul class="space-y-1">
                            <ULink
                                to="/backoffice"
                                variant="tertiary"
                                class="flex px-3 py-3 rounded-lg hover:bg-brand-600 hover:text-white hover:no-underline focus:outline-none focus:ring-0"
                                exact-active-class="bg-brand-600 text-white"
                            >
                                <HomeIcon class="w-7 h-7 mr-3" />
                                Tableau de bord
                            </ULink>

                            <li>
                                <ULink
                                    to="/backoffice/owners"
                                    variant="tertiary"
                                    class="block px-3 py-3 rounded-lg hover:bg-brand-600 hover:text-white hover:no-underline focus:outline-none focus:ring-0"
                                    exact-active-class="bg-brand-600 text-white"
                                >
                                    <BuildingIcon class="w-7 h-7 mr-3" />
                                    Hôtes
                                </ULink>
                            </li>

                            <li>
                                <ULink
                                    to="/backoffice/clients"
                                    variant="tertiary"
                                    class="block px-3 py-3 rounded-lg hover:bg-brand-600 hover:text-white hover:no-underline focus:outline-none focus:ring-0"
                                    exact-active-class="bg-brand-600 text-white"
                                >
                                    <UserIcon class="w-7 h-7 mr-3" />
                                    Clients
                                </ULink>
                            </li>

                            <li>
                                <ULink
                                    to="/backoffice/admins"
                                    variant="tertiary"
                                    class="block px-3 py-3 rounded-lg hover:bg-brand-600 hover:text-white hover:no-underline focus:outline-none focus:ring-0"
                                    exact-active-class="bg-brand-600 text-white"
                                >
                                    <BuildingIcon class="w-7 h-7 mr-3" />
                                    Admins
                                </ULink>
                            </li>

                            <li>
                                <ULink
                                    to="/backoffice/bookings"
                                    variant="tertiary"
                                    class="block px-3 py-3 rounded-lg hover:bg-brand-600 hover:text-white hover:no-underline focus:outline-none focus:ring-0"
                                    exact-active-class="bg-brand-600 text-white"
                                >
                                    <CoinsHandIcon class="w-7 h-7 mr-3" />
                                    Réservations
                                </ULink>
                            </li>

                            <li>
                                <ULink
                                    to="/backoffice/themes"
                                    variant="tertiary"
                                    class="block px-3 py-3 rounded-lg hover:bg-brand-600 hover:text-white hover:no-underline focus:outline-none focus:ring-0"
                                    exact-active-class="bg-brand-600 text-white"
                                >
                                    <LightningIcon class="w-7 h-7 mr-3" />
                                    Themes
                                </ULink>
                            </li>

                            <li>
                                <ULink
                                    to="/backoffice/accommodations"
                                    variant="tertiary"
                                    class="block px-3 py-3 rounded-lg hover:bg-brand-600 hover:text-white hover:no-underline focus:outline-none focus:ring-0"
                                    exact-active-class="bg-brand-600 text-white"
                                >
                                    <PinIcon class="w-7 h-7 mr-3" />
                                    Logements
                                </ULink>
                            </li>

                            <li>
                                <ULink
                                    to="/backoffice/tickets"
                                    variant="tertiary"
                                    class="block px-3 py-3 rounded-lg hover:bg-brand-600 hover:text-white hover:no-underline focus:outline-none focus:ring-0"
                                    exact-active-class="bg-brand-600 text-white"
                                >
                                    <TicketIcon class="w-7 h-7 mr-3" />
                                    Tickets
                                </ULink>
                            </li>
                            <li>
                                <ULink
                                    to="/backoffice/owner-requests"
                                    variant="tertiary"
                                    class="block px-3 py-3 rounded-lg hover:bg-brand-600 hover:text-white hover:no-underline focus:outline-none focus:ring-0"
                                    exact-active-class="bg-brand-600 text-white"
                                >
                                    <MessageIcon class="w-7 h-7 mr-3" />
                                    Demandes propriétaire
                                </ULink>
                            </li>
                        </ul>
                    </nav>
                </div>

                <div class="px-7 py-3 border-t flex flex-col gap-1">
                    <div
                        class="flex items-center gap-3 cursor-pointer hover:bg-gray-50 px-3 py-2 rounded-lg"
                        @click="goToProfile"
                    >
                        <img
                            :src="user?.avatar || '/default-avatar.jpg'"
                            alt="Avatar"
                            class="w-10 h-10 rounded-full object-cover"
                        />
                        <div class="flex-1 text-sm">
                            <div class="font-semibold">{{ user?.firstName }} {{ user?.lastName }}</div>
                            <div class="text-xs text-gray-500">{{ formattedRole }}</div>
                        </div>
                    </div>

                    <button
                        class="flex items-center px-3 py-3 rounded-lg hover:bg-brand-600 hover:text-white focus:outline-none focus:ring-0 w-full text-left"
                        @click="logout"
                    >
                        <LogoutIcon class="w-7 h-7 mr-3" />
                        Déconnexion
                    </button>
                </div>
            </aside>

            <div class="flex-1 w-full">
                <main class="py-12 ml-[23rem] mr-8 pr-6 flex flex-col gap-10 max-w-screen overflow-x-hidden">
                    <BreadcrumbElement />
                    <slot />
                </main>
            </div>
        </div>
    </NuxtLayout>
</template>
