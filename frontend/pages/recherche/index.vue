<script setup lang="ts">
    import AccommodationCards from '~/components/AccommodationCards.vue';
    import { useAccommodationSearch } from '~/composables/useAccommodationSearch';
    import ULoading from '~/components/atoms/ULoading.vue';
    import { useSearchResultsStore } from '~/stores/searchResults';

    const { accommodationItems, isLoading, hasResults, loadSearchResults } = useAccommodationSearch();
    const searchResultsStore = useSearchResultsStore();

    useSeoMeta({
        title: "PopnBed - Recherche d'hébergements inspirés de films",
        description: "Trouvez l'hébergement parfait inspiré de vos films et séries préférés.",
    });

    // Observer les changements dans l'URL pour recharger les résultats
    const route = useRoute();
    watch(
        () => route.query,
        () => {
            // Effacer les résultats précédents quand les paramètres changent
            searchResultsStore.clearResults();
            loadSearchResults();
        },
        { immediate: true }
    );
</script>

<template>
    <main class="w-full h-full flex flex-grow flex-col">
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
        </div>
        <section class="w-full pt-12">
            <div class="text-center mb-10 max-w-7xl w-full mx-auto px-4">
                <h2 class="text-h2 pb-8">Recherche</h2>

                <!-- Afficher un spinner de chargement pendant le chargement -->
                <div v-if="isLoading" class="flex justify-center items-center py-16">
                    <ULoading />
                </div>

                <!-- Afficher un message quand il n'y a pas de résultats -->
                <div v-else-if="!hasResults" class="py-16 text-center">
                    <p class="text-body-lg text-gray-600">Aucun résultat ne correspond à votre recherche.</p>
                    <p class="text-body-md text-gray-500 mt-2">Essayez avec d'autres critères ou destinations.</p>
                </div>

                <!-- Afficher les résultats de recherche -->
                <AccommodationCards v-else :items="accommodationItems" />
            </div>
        </section>
        <UFooter />
    </main>
</template>
