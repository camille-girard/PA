import { ref, computed, type Ref } from 'vue';

interface PaginationOptions {
    itemsPerPage?: number;
    initialPage?: number;
}

interface PaginationMeta {
    currentPage: number;
    totalPages: number;
    totalItems: number;
    itemsPerPage: number;
    hasNextPage: boolean;
    hasPreviousPage: boolean;
}

export function usePagination<T>(items: Ref<T[]>, options: PaginationOptions = {}) {
    const { itemsPerPage = 10, initialPage = 1 } = options;

    const currentPage = ref(initialPage);
    const perPage = ref(itemsPerPage);

    const totalItems = computed(() => items.value?.length || 0);
    const totalPages = computed(() => Math.ceil(totalItems.value / perPage.value) || 1);

    const paginatedItems = computed(() => {
        if (!items.value || !Array.isArray(items.value)) return [];
        const startIndex = (currentPage.value - 1) * perPage.value;
        const endIndex = startIndex + perPage.value;
        return items.value.slice(startIndex, endIndex);
    });

    const meta = computed(
        (): PaginationMeta => ({
            currentPage: currentPage.value,
            totalPages: totalPages.value,
            totalItems: totalItems.value,
            itemsPerPage: perPage.value,
            hasNextPage: currentPage.value < totalPages.value,
            hasPreviousPage: currentPage.value > 1,
        })
    );

    const goToPage = (page: number) => {
        if (page >= 1 && page <= totalPages.value) {
            currentPage.value = page;
        }
    };

    const nextPage = () => {
        if (meta.value.hasNextPage) {
            currentPage.value++;
        }
    };

    const previousPage = () => {
        if (meta.value.hasPreviousPage) {
            currentPage.value--;
        }
    };

    const firstPage = () => {
        currentPage.value = 1;
    };

    const lastPage = () => {
        currentPage.value = totalPages.value;
    };

    const setItemsPerPage = (newPerPage: number) => {
        perPage.value = newPerPage;
        currentPage.value = 1; // Reset to first page when changing items per page
    };

    return {
        currentPage,
        perPage,
        totalItems,
        totalPages,
        paginatedItems,
        meta,
        goToPage,
        nextPage,
        previousPage,
        firstPage,
        lastPage,
        setItemsPerPage,
    };
}
