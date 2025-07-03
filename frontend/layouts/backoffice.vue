<script setup lang="ts">
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
    <div class="flex min-h-screen bg-gray-25 text-gray-800">
        <!-- Sidebar -->
        <aside
            class="w-80 bg-white border-r shadow-sm flex flex-col justify-between min-h-screen fixed left-0 top-0 bottom-0"
        >
            <div>
                <ULink to="/" class="py-10 flex items-center justify-center space-x-2">
                    <ULogo class="w-10 h-10" />
                    <span class="font-semibold text-2xl">PopnBed</span>
                </ULink>

                <nav class="px-7 mt-4">
                    <ul class="space-y-2">
                        <ULink
                            to="/backoffice"
                            class="flex px-3 py-3 rounded-lg hover:bg-brand-600 hover:text-white hover:no-underline focus:outline-none focus:ring-0"
                            exact-active-class="bg-brand-600 text-white"
                        >
                            <HomeIcon class="w-7 h-7 mr-3" />
                            Tableau de bord
                        </ULink>

                        <li>
                            <ULink
                                to="/backoffice/owners"
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
                                class="block px-3 py-3 rounded-lg hover:bg-brand-600 hover:text-white hover:no-underline focus:outline-none focus:ring-0"
                                exact-active-class="bg-brand-600 text-white"
                            >
                                <UserIcon class="w-7 h-7 mr-3" />
                                Clients
                            </ULink>
                        </li>

                        <li>
                            <ULink
                                to="/backoffice/bookings"
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
                                class="block px-3 py-3 rounded-lg hover:bg-brand-600 hover:text-white hover:no-underline focus:outline-none focus:ring-0"
                                exact-active-class="bg-brand-600 text-white"
                            >
                                <MessageIcon class="w-7 h-7 mr-3" />
                                Tickets
                            </ULink>
                        </li>
                    </ul>
                </nav>
            </div>

            <div class="px-7 py-6 border-t flex flex-col gap-2">
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
                    @click="logout"
                    class="flex items-center px-3 py-3 rounded-lg hover:bg-brand-600 hover:text-white focus:outline-none focus:ring-0 w-full text-left"
                >
                    <LogoutIcon class="w-7 h-7 mr-3" />
                    Déconnexion
                </button>
            </div>
        </aside>

        <div class="flex-1 ml-80">
            <main class="py-12 px-16 flex flex-col gap-10 overflow-x-hidden">
                <slot />
            </main>
        </div>
    </div>
</template>
