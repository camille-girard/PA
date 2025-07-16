<script setup lang="ts">
    import { useThemes } from '~/composables/useThemes';
    import type { AccommodationItemDto } from '~/types/dtos/accommodation_item.dto';
    import type { ThemeWithAccommodationsDto } from '~/types/dtos/theme_with_accommodations.dto';
    import AccommodationCards from '~/components/AccommodationCards.vue';

    const route = useRoute();
    const { getThemeBySlug } = useThemes();

    const theme = ref<ThemeWithAccommodationsDto | null>(null);
    const accommodations = ref<AccommodationItemDto[]>([]);

    onMounted(async () => {
        if (typeof route.params.slug === 'string') {
            const themeData = await getThemeBySlug(route.params.slug);
            if (themeData && themeData.theme) {
                theme.value = themeData.theme;

                accommodations.value =
                    themeData.theme.accommodations?.map((accommodation) => ({
                        id: accommodation.id,
                        title: accommodation.name,
                        image: accommodation.images[0]?.url || 'https://via.placeholder.com/400x250',
                        slug: themeData.theme.slug,
                    })) || [];

                useSeoMeta({
                    title: `${theme.value.name} - PopnBed`,
                    description: `Découvrez nos hébergements inspirés de ${theme.value.name}`,
                });
            }
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
                        <h1 class="text-h1">Thématiques - {{ theme?.name || 'Chargement...' }}</h1>
                        <p class="text-body-md mt-4">
                            Trouvez le lieu parfait inspiré de vos films et séries préférés.
                        </p>
                    </div>
                </div>
            </section>
            <section class="w-full pt-12">
                <div class="text-center mb-10">
                    <h2 class="text-h2">{{ theme?.name || 'Chargement...' }}</h2>
                </div>
                <AccommodationCards :items="accommodations" />
            </section>
        </div>
        <UFooter />
    </main>
</template>
