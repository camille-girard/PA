import { defineStore } from 'pinia';

export const useSearchResultsStore = defineStore('searchResults', {
    state: () => ({
        results: null as any,
    }),
    actions: {
        setResults(data: any) {
            this.results = data;
        },
        clearResults() {
            this.results = null;
        },
    },
});
