import { useToast } from '~/composables/useToast';
import { useSearchResultsStore } from '~/stores/searchResults';
import { useSearchService } from '~/services/search.service';
import type { AccommodationItemDto } from '~/types/dtos/accommodation_item.dto';
import type { AccommodationDto } from '~/types/dtos/accommodation.dto';

export function useAccommodationSearch() {
    const searchResultsStore = useSearchResultsStore();
    const toast = useToast();
    const isLoading = ref(false);
    const error = ref<Error | null>(null);
    const hasResults = computed(() => {
        return searchResultsStore.results?.accommodations && searchResultsStore.results.accommodations.length > 0;
    });

    const accommodationItems = computed<AccommodationItemDto[]>(() => {
        const results = searchResultsStore.results;
        if (!results?.accommodations) {
            return [];
        }

        return results.accommodations.map((accommodation: AccommodationDto) => ({
            id: accommodation.id || 0,
            title: accommodation.name || '',
            image: accommodation.images?.[0]?.url || '',
            price: accommodation.price || 0,
            slug: accommodation.theme?.slug || '',
        }));
    });

    // Méthode pour rechercher par query string
    const searchByQuery = async (query: string): Promise<void> => {
        if (!query.trim()) {
            return;
        }

        const searchService = useSearchService();
        isLoading.value = true;
        error.value = null;

        try {
            const { data, error: fetchError } = await searchService.searchAccommodations(query);

            if (fetchError.value) {
                throw new Error(fetchError.value.message || 'Une erreur est survenue lors de la recherche');
            }

            if (data.value) {
                searchResultsStore.setResults({ accommodations: data.value });
            }
        } catch (err: unknown) {
            const errorObj = err as Error;
            error.value = errorObj;
            toast.error('Erreur de recherche', errorObj.message || 'Une erreur est survenue lors de la recherche');
            console.error('Erreur lors de la recherche:', errorObj);
        } finally {
            isLoading.value = false;
        }
    };

    // Méthode pour charger les résultats à partir d'une recherche existante
    const loadSearchResults = async (): Promise<void> => {
        const route = useRoute();

        // Si nous avons déjà des résultats dans le store et que nous venons de faire une recherche,
        // on ne refait pas de requête (évite la double requête)
        if (searchResultsStore.results) {
            return;
        }

        // Si nous avons des paramètres de recherche dans l'URL, on refait la recherche
        const destination = route.query.destination as string;
        const arrivalDate = route.query.arrivalDate as string;
        const departureDate = route.query.departureDate as string;
        const amountTravelers = route.query.amountTravelers as string;

        if (destination) {
            // Utiliser le service de recherche complet avec tous les paramètres
            const searchService = useSearchService();
            isLoading.value = true;
            error.value = null;

            try {
                const searchData = {
                    destination: destination.trim(),
                    arrivalDate: arrivalDate || undefined,
                    departureDate: departureDate || undefined,
                    amountTravelers: amountTravelers ? Number(amountTravelers) : undefined,
                };

                const { data, error: fetchError } = await searchService.submitSearch(searchData);

                if (fetchError.value) {
                    throw new Error(fetchError.value.message || 'Une erreur est survenue lors de la recherche');
                }

                if (data.value) {
                    searchResultsStore.setResults(data.value);
                }
            } catch (err: unknown) {
                const errorObj = err as Error;
                error.value = errorObj;
                toast.error('Erreur de recherche', errorObj.message || 'Une erreur est survenue lors de la recherche');
                console.error('Erreur lors de la recherche:', errorObj);
            } finally {
                isLoading.value = false;
            }
        } else {
            // Si pas de paramètres de recherche, on efface les résultats
            searchResultsStore.clearResults();
        }
    };

    return {
        accommodationItems,
        isLoading,
        error,
        hasResults,
        searchByQuery,
        loadSearchResults,
    };
}
