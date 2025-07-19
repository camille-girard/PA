<script setup lang="ts">
    import { useThemes } from '~/composables/useThemes';
    import AccommodationCards from '~/components/AccommodationCards.vue';

    useSeoMeta({
        title: "PopnBed - Explorez nos thématiques d'hébergements inspirés de films",
        description: 'Découvrez des logements thématiques uniques inspirés de films et séries iconiques',
    });

    const { themeSections: ThemeSections, fetchThemesWithAccommodations } = useThemes();

    onMounted(async () => {
        await fetchThemesWithAccommodations();
    });
</script>

<template>
    <main class="w-full h-full flex-grow flex flex-col">
        <UHeader />
        <div class="max-w-7xl w-full mx-auto pt-8 px-4 flex-grow">
            <section class="w-full pt-8">
                <div class="py-20 rounded-2xl flex items-center justify-center">
                    <div class="text-center">
                        <h1 class="text-h1">Explorez nos thématiques</h1>
                        <p class="text-body-md mt-4">
                            Trouvez le lieu parfait inspiré de vos films et séries préférés.
                        </p>
                        <SearchBar />
                    </div>
                </div>
            </section>
            <section v-for="section in ThemeSections" :key="section.title" class="w-full pt-12">
                <div class="text-center mb-10">
                    <h2 class="text-h2">{{ section.title }}</h2>
                </div>
                <AccommodationCards :items="section.items?.slice(0, 3)" />
                <div v-if="section.items?.length > 3" class="flex justify-end hover:underline mt-5">
                    <NuxtLink
                        :to="`/thematiques/${section.slug}`"
                        class="inline-flex items-center text-orange-600 text-body-sm font-medium"
                        >Voir plus
                        <svg
                            class="w-4 h-4 ml-1"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                        </svg>
                    </NuxtLink>
                </div>
            </section>
        </div>
        <UFooter />
    </main>
</template>
