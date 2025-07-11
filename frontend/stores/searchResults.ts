import { defineStore } from 'pinia';
import type { AccommodationDto } from '~/types/dtos/accommodation.dto';

interface SearchResults {
    accommodations: AccommodationDto[];
}

export const useSearchResultsStore = defineStore('searchResults', {
    state: () => ({
        results: null as SearchResults | null,
    }),
    actions: {
        setResults(data: SearchResults) {
            this.results = data;
        },
        clearResults() {
            this.results = null;
        },
    },
});
