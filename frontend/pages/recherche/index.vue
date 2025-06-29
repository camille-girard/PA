<script setup lang="ts">
    import { useSearchResultsStore } from '~/stores/searchResults';

    const searchResultsStore = useSearchResultsStore();

    const accommodationsForDisplay = ref<any[]>([]);

    watchEffect(() => {
        const results = searchResultsStore.results;
        if (results?.accommodations) {
            accommodationsForDisplay.value = results.accommodations.map((accommodation: any) => ({
                title: accommodation.name || '',
                image: accommodation.images?.[0]?.url || '',
                id: accommodation.id || 0,
                slug: accommodation.theme.slug || '',
            }));
        } else {
            accommodationsForDisplay.value = [];
        }
    });

    const items = accommodationsForDisplay;
</script>

<template>
    <main>
        <UHeader />
        <div class="max-w-7xl w-full mx-auto pt-8 px-4">
            <section class="w-full pt-8">
                <div class="py-20 rounded-2xl flex items-center justify-center relative">
                    <div class="text-center z-10">
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
                <LocationCards :items="items" />
            </div>
        </section>
        <UFooter />
    </main>
</template>
