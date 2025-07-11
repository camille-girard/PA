import { ref } from 'vue';
import type { Ref } from 'vue';
import { useToast } from '~/composables/useToast';

export interface MapSuggestion {
    id: string;
    place_name: string;
    geometry: {
        coordinates: [number, number]; // [longitude, latitude]
    };
}

interface UseAddressSuggestions {
    suggestions: Ref<MapSuggestion[]>;
    fetchSuggestions: (query: string) => Promise<void>;
    selectSuggestion: (suggestion: MapSuggestion) => {
        address: string;
        latitude: number;
        longitude: number;
    };
}

export function useAddressSuggestions(): UseAddressSuggestions {
    const config = useRuntimeConfig();
    const suggestions = ref<MapSuggestion[]>([]);

    // Debounced fetch suggestions
    const debounceTimer = ref<ReturnType<typeof setTimeout> | null>(null);

    async function fetchSuggestions(query: string): Promise<void> {
        if (debounceTimer.value) clearTimeout(debounceTimer.value);
        if (!query) {
            suggestions.value = [];
            return;
        }

        try {
            const encodedQuery = encodeURIComponent(query);
            const accessToken = config.public.mapboxToken;

            const url = `https://api.mapbox.com/geocoding/v5/mapbox.places/${encodedQuery}.json?access_token=${accessToken}&country=fr&autocomplete=true&limit=5`;

            const res = await fetch(url);
            const data = await res.json();
            suggestions.value = data.features || [];
        } catch (error) {
            console.error('Error fetching address suggestions:', error);
            suggestions.value = [];

            // Afficher un toast d'erreur
            const toast = useToast();
            toast.error('Erreur de recherche', "Impossible de récupérer les suggestions d'adresses.");
        }
    }

    function selectSuggestion(suggestion: MapSuggestion) {
        const address = suggestion.place_name;
        const latitude = suggestion.geometry.coordinates[1];
        const longitude = suggestion.geometry.coordinates[0];

        // Clear suggestions after selection
        suggestions.value = [];

        return { address, latitude, longitude };
    }

    return {
        suggestions,
        fetchSuggestions,
        selectSuggestion,
    };
}
