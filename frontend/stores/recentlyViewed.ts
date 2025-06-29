import { defineStore } from 'pinia';
import type { Accommodation } from '~/types/accommodation';

interface RecentlyViewedState {
    items: Accommodation[];
    maxItems: number;
}

export const useRecentlyViewedStore = defineStore('recentlyViewed', {
    state: (): RecentlyViewedState => ({
        items: [],
        maxItems: 3,
    }),

    actions: {
        addAccommodation(accommodation: Accommodation) {
            if (!accommodation || !accommodation.id) return;

            const existingIndex = this.items.findIndex((item) => item.id === accommodation.id);

            if (existingIndex !== -1) {
                this.items.splice(existingIndex, 1);
            }

            this.items.unshift(accommodation);

            if (this.items.length > this.maxItems) {
                this.items = this.items.slice(0, this.maxItems);
            }
        },

        getRecentlyViewedExcept(excludeId: string | number) {
            const normalizedExcludeId = typeof excludeId === 'string' ? parseInt(excludeId) : excludeId;

            const filteredItems = this.items.filter((item) => {
                const normalizedItemId = typeof item.id === 'string' ? parseInt(item.id as string) : item.id;
                return normalizedItemId !== normalizedExcludeId;
            });

            return filteredItems.map((item) => ({
                title: item.name,
                image: item.images && item.images.length > 0 ? item.images[0].url : '/placeholder-image.jpg',
                id: item.id,
                slug: item.id.toString(),
                price: item.price,
            }));
        },
    },

    persist: true,
});
