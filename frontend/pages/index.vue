<script setup lang="ts">
    import SearchIcon from '~/components/atoms/icons/SearchIcon.vue';

    const authStore = useAuthStore();

    const amountTravelers = ref<number | undefined>(undefined);
    const arrivalDate = ref();
    const departureDate = ref();
</script>

<template>
    <main class="w-full h-full">
        <header class="h-20 w-full fixed top-0 flex items-center">
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
                <UButton v-if="!authStore.isAuthenticated" size="lg" @click="navigateTo('/login')">S'identifier</UButton>
                <LogoutButton v-else />
            </div>
        </header>
        <section class="max-w-7xl w-full mx-auto">
            <section class="w-full pt-32">
                <div class="py-20 rounded-2xl flex items-center justify-center bg-red-200 relative">
                    <div class="w-full h-full rounded-2xl overflow-hidden absolute top-0 left-0">
                        <img
                            src="https://toploc.com/blog/wp-content/uploads/2016/06/Maison-de-hobbit-achevee-%C2%A9simondale.net_.jpg"
                            class="w-full h-full object-cover blur-sm"
                        />
                    </div>
                    <div class="text-center z-10">
                        <h1 class="text-4xl font-semibold text-primary-on-brand">
                            Séjourner dans un lieu inspiré de films
                        </h1>
                        <p class="text-primary-on-brand text-lg">
                            Trouver et réservez des hébergements uniques à thème cinématographique
                        </p>

                        <div class="mt-14 max-w-screen-sm space-y-3">
                            <UInput placeholder="Destination, thématique" type="text" :icon="SearchIcon" />
                            <div class="flex w-full justify-between gap-2">
                                <UDatePicker v-model="arrivalDate" placeholder="Arrivée" type="date" class="w-full" />
                                <UDatePicker v-model="departureDate" placeholder="Départ" type="date" class="w-full" />
                                <UInputNumber
                                    v-model="amountTravelers"
                                    placeholder="Voyageurs"
                                    type="number"
                                    :min="1"
                                    class="w-full"
                                />
                            </div>
                        </div>

                        <UButton :icon="SearchIcon" icon-position="leading" class="mx-auto mt-8">Rechercher</UButton>
                    </div>
                </div>
            </section>
        </section>
    </main>
</template>

