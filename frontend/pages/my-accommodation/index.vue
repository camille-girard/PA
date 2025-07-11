<script setup lang="ts">
    import { ref, onMounted } from 'vue';
    import RentalCards from '~/components/RentalCards.vue';
    import type { AccommodationDto } from '~/types/dtos/accommodation.dto';

    const accommodations = ref([]);
    const isLoading = ref(true);
    const error = ref(null);

    const config = useRuntimeConfig();

    onMounted(async () => {
        try {
            const res = await fetch(`${config.public.apiUrl}/api/accommodations/me`, {
                credentials: 'include',
            });

            if (!res.ok) {
                throw new Error(`Erreur serveur: ${res.statusText}`);
            }

            const json = await res.json();

            accommodations.value = json.accommodations.map((acc: AccommodationDto) => ({
                title: acc.name,
                image: acc.images?.[0]?.url || 'https://via.placeholder.com/400x250',
                slug: acc.id,
                price: acc.price,
            }));
        } catch (err: unknown) {
            error.value = err instanceof Error ? err.message : 'Une erreur est survenue';
        } finally {
            isLoading.value = false;
        }
    });
</script>

<template>
    <main class="w-full h-full">
        <UHeader />
        <div class="max-w-7xl w-full mx-auto pt-8 px-4">
            <section class="w-full pt-8">
                <div class="py-20 rounded-2xl flex items-center justify-center">
                    <div class="text-center">
                        <h1 class="text-h1">Mes hébergements</h1>
                        <p class="mt-4">Gérez vos logements proposés à la location</p>
                    </div>
                </div>

                <div v-if="isLoading" class="text-center text-gray-500">Chargement...</div>
                <div v-else-if="error" class="text-center text-red-500">{{ error }}</div>
                <div v-else-if="accommodations.length === 0" class="text-center text-gray-400 text-lg">
                    Vous n’avez encore publié aucun logement.
                </div>
                <div v-else>
                    <RentalCards :items="accommodations" link-prefix="/my-accommodation" />
                </div>

                <div class="mt-10 text-center">
                    <NuxtLink to="/my-accommodation/create">
                        <UButton class="mx-auto">Ajouter un logement</UButton>
                    </NuxtLink>
                </div>
            </section>
        </div>
        <UFooter />
    </main>
</template>
