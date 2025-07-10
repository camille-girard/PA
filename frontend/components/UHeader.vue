<script setup lang="ts">
    import { useAuthStore } from '~/stores/auth';
    import type { MenuItem } from '~/components/molecules/UDropdown.vue';
    import UserIcon from '~/components/atoms/icons/UserIcon.vue';
    import MessageChatCircleIcon from '~/components/atoms/icons/MessageChatCircleIcon.vue';
    import ClipboardCheckIcon from '~/components/atoms/icons/ClipboardCheckIcon.vue';
    import HomeIcon from '~/components/atoms/icons/HomeIcon.vue';
    import LogoutIcon from '~/components/atoms/icons/LogoutIcon.vue';
    import SettingsIcon from '~/components/atoms/icons/SettingsIcon.vue';
    import TicketIcon from '~/components/atoms/icons/TicketIcon.vue';

    const authStore = useAuthStore();
    const isMenuOpen = ref(false);

    /**
     * Génère les initiales de l'utilisateur à partir de son nom et prénom
     */
    const userInitials = computed(() => {
        if (!authStore.user) return '';

        const { firstName, lastName } = authStore.user;

        if (firstName && lastName) {
            return `${firstName.charAt(0)}${lastName.charAt(0)}`.toUpperCase();
        }

        if (firstName) {
            return firstName.charAt(0).toUpperCase();
        }

        return '';
    });

    const userAvatarUrl = computed(() => {
        return authStore.user?.avatar || '';
    });

    const canViewAccommodation = computed(() => {
        if (!authStore.isAuthenticated || !authStore.user?.roles) {
            return false;
        }

        return authStore.user.roles.some((role) => role === 'ROLE_OWNER' || role === 'ROLE_ADMIN');
    });

    const isAdmin = computed(() => {
        return authStore.isAdmin;
    });

    const profileMenuItems = computed<MenuItem[]>(() => {
        const items: MenuItem[] = [];

        if (isAdmin.value) {
            // Menu pour les administrateurs
            return [
                {
                    label: 'Administration',
                    icon: SettingsIcon,
                    action: async () => {
                        await navigateTo('/backoffice');
                    },
                },
                {
                    label: 'Déconnexion',
                    icon: LogoutIcon,
                    action: async () => {
                        await authStore.logout();
                    },
                },
            ];
        } else {
            items.push(
                {
                    label: 'Profil',
                    icon: UserIcon,
                    action: async () => {
                        await navigateTo('/profile');
                    },
                },
                {
                    label: 'Messages',
                    icon: MessageChatCircleIcon,
                    action: async () => {
                        await navigateTo('/messages');
                    },
                },
                {
                    label: 'Réservations',
                    icon: ClipboardCheckIcon,
                    action: async () => {
                        await navigateTo('/booking');
                    },
                },
                {
                    label: 'Support',
                    icon: TicketIcon,
                    action: async () => {
                        await navigateTo('/my-tickets');
                    },
                }
            );

            if (canViewAccommodation.value) {
                items.push({
                    label: 'Hébergement',
                    icon: HomeIcon,
                    action: async () => {
                        await navigateTo('/my-accommodation');
                    },
                });
            } else {
                items.push({
                    label: 'Devenir hôte',
                    icon: HomeIcon,
                    action: async () => {
                        await navigateTo('/owner-request');
                    },
                });
            }
            if (authStore.user?.roles?.includes('ROLE_OWNER')) {
                items.push({
                    label: 'Gestion des réservations',
                    icon: ClipboardCheckIcon,
                    action: async () => {
                        await navigateTo('/booking/manage-booking');
                    },
                });
            }
        }

        items.push({
            label: 'Déconnexion',
            icon: LogoutIcon,
            action: async () => {
                await authStore.logout();
            },
        });

        return items;
    });
</script>

<template>
    <header class="bg-white py-5 w-full fixed top-0 z-10 px-4">
        <div class="max-w-7xl mx-auto w-full flex items-center justify-between">
            <NuxtLink to="/" class="flex items-center gap-2">
                <ULogo />
                <span class="text-body-sm font-semibold">PopnBed</span>
            </NuxtLink>

            <nav class="hidden md:flex items-center gap-6">
                <ULink to="/thematiques" variant="tertiary">Thématiques</ULink>
                <ULink to="/explorer" variant="tertiary">Explorer</ULink>
            </nav>

            <div class="hidden md:block relative">
                <UButton v-if="!authStore.isAuthenticated" size="lg" @click="navigateTo('/login')">
                    Se connecter
                </UButton>

                <div v-else>
                    <UDropdown :menu-items="profileMenuItems" position="bottom-right">
                        <template #trigger="{ toggle }">
                            <button
                                class="focus:outline-none transition-transform hover:scale-105 border-2 border-transparent hover:border-gray-200 rounded-full p-0.5"
                                @click="toggle"
                            >
                                <UAvatar
                                    size="lg"
                                    :image-src="userAvatarUrl"
                                    :text="userInitials"
                                    status-icon="false"
                                />
                            </button>
                        </template>
                    </UDropdown>
                </div>
            </div>

            <button class="md:hidden" @click="isMenuOpen = !isMenuOpen">
                <svg
                    class="w-6 h-6 text-gray-700"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    viewBox="0 0 24 24"
                >
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
        </div>

        <transition name="fade">
            <div v-if="isMenuOpen" class="md:hidden mt-4 px-4 space-y-4 pt-6">
                <ULink to="/thematiques" class="block text-gray-700 font-medium">Thématiques</ULink>
                <ULink to="/explorer" class="block text-gray-700 font-medium">Explorer</ULink>

                <template v-if="authStore.isAuthenticated">
                    <div class="flex items-center gap-3 py-3 mb-2 border-b border-gray-100">
                        <UAvatar size="md" :image-src="userAvatarUrl" :text="userInitials" status-icon="false" />
                        <div>
                            <span class="text-sm font-medium">
                                {{ authStore.user?.firstName }} {{ authStore.user?.lastName }}
                            </span>
                            <span
                                v-if="isAdmin"
                                class="text-xs ml-2 px-2 py-0.5 bg-blue-100 text-blue-800 rounded-full"
                            >
                                Admin
                            </span>
                        </div>
                    </div>
                    <div v-for="(item, index) in profileMenuItems" :key="index" class="py-1">
                        <div
                            class="flex items-center gap-2 cursor-pointer text-gray-700 font-medium"
                            @click="item.action?.()"
                        >
                            <component :is="item.icon" class="w-5 h-5" />
                            {{ item.label }}
                        </div>
                    </div>
                </template>

                <div v-else>
                    <UButton size="lg" class="w-full" @click="navigateTo('/login')"> Se connecter </UButton>
                </div>
            </div>
        </transition>
    </header>
</template>
