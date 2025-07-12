<script setup lang="ts">
    import '~/types/accommodation';
    import type { AccommodationDto } from '~/types/dtos/accommodation.dto';

    useSeoMeta({
        title: 'PopnBed - Tendances du moment',
        description: 'Découvrez les hébergements les plus populaires inspirés de films et séries',
    });

    const trending = ref<Accommodation[]>([]);

    onMounted(async () => {
        const { $api } = useNuxtApp();
        const response = await useAuthFetch<AccommodationDto[]>($api('/api/accommodations/'));

        if (response.data.value && Array.isArray(response.data.value)) {
            trending.value = response.data.value.map((acc: AccommodationDto) => ({
                ...acc,
                id: acc.id,
                title: acc.name,
                slug: acc.theme?.slug || acc.theme?.name?.toLowerCase().replace(/\s+/g, '-') || 'accommodation',
                image: acc.images?.[0]?.url || 'https://via.placeholder.com/400x250',
            }));
        }
    });
</script>

<template>
    <main>
        <UHeader />
        <div class="max-w-7xl w-full mx-auto pt-8 px-4">
            <section class="w-full pt-8">
                <div class="py-20 rounded-2xl flex items-center justify-center">
                    <div class="text-center">
                        <h1 class="text-h1">Tendances du moment</h1>
                        <p class="text-body-md mt-4">Les logements les plus populaires cette semaine</p>
                    </div>
                </div>
            </section>
            <section class="w-full pt-12">
                <div class="text-center mb-10">
                    <h2 class="text-h2">Les coups de coeur du moment</h2>
                </div>
                <AccommodationCards :items="trending" />
            </section>
        </div>
        <UFooter />
    </main>
</template>
