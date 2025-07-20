<script setup lang="ts">
    import { computed } from 'vue';

    interface PaginationMeta {
        currentPage: number;
        totalPages: number;
        totalItems: number;
        itemsPerPage: number;
        hasNextPage: boolean;
        hasPreviousPage: boolean;
    }

    interface Props {
        meta: PaginationMeta;
        showItemsPerPage?: boolean;
        itemsPerPageOptions?: number[];
    }

    const props = withDefaults(defineProps<Props>(), {
        showItemsPerPage: true,
        itemsPerPageOptions: () => [5, 10, 20, 50],
    });

    const emit = defineEmits<{
        goToPage: [page: number];
        nextPage: [];
        previousPage: [];
        firstPage: [];
        lastPage: [];
        setItemsPerPage: [itemsPerPage: number];
    }>();

    const getVisiblePages = () => {
        const totalPages = props.meta.totalPages;
        const currentPage = props.meta.currentPage;
        const visiblePages: (number | string)[] = [];

        if (totalPages <= 7) {
            for (let i = 1; i <= totalPages; i++) {
                visiblePages.push(i);
            }
        } else {
            visiblePages.push(1);

            if (currentPage > 4) {
                visiblePages.push('...');
            }

            const start = Math.max(2, currentPage - 2);
            const end = Math.min(totalPages - 1, currentPage + 2);

            for (let i = start; i <= end; i++) {
                visiblePages.push(i);
            }

            if (currentPage < totalPages - 3) {
                visiblePages.push('...');
            }

            if (totalPages > 1) {
                visiblePages.push(totalPages);
            }
        }

        return visiblePages;
    };

    const startItem = computed(() => {
        return (props.meta.currentPage - 1) * props.meta.itemsPerPage + 1;
    });

    const endItem = computed(() => {
        return Math.min(props.meta.currentPage * props.meta.itemsPerPage, props.meta.totalItems);
    });
</script>

<template>
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4 mt-6">
        <div class="flex items-center gap-4">
            <div class="text-sm text-gray-700">
                Affichage de {{ startItem }} à {{ endItem }} sur {{ meta.totalItems }} résultats
            </div>
            <div v-if="showItemsPerPage" class="flex items-center gap-2">
                <span class="text-sm text-gray-700">Éléments par page:</span>
                <select
                    :value="meta.itemsPerPage"
                    class="w-20 px-2 py-1 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                    @change="(event) => emit('setItemsPerPage', parseInt((event.target as HTMLSelectElement).value))"
                >
                    <option v-for="option in itemsPerPageOptions" :key="option" :value="option">
                        {{ option }}
                    </option>
                </select>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <button
                :disabled="!meta.hasPreviousPage"
                class="px-3 py-1 border border-gray-300 rounded text-sm disabled:opacity-50 disabled:cursor-not-allowed hover:bg-orange-50 hover:border-orange-300 transition-colors"
                @click="emit('firstPage')"
            >
                ««
            </button>
            <button
                :disabled="!meta.hasPreviousPage"
                class="px-3 py-1 border border-gray-300 rounded text-sm disabled:opacity-50 disabled:cursor-not-allowed hover:bg-orange-50 hover:border-orange-300 transition-colors"
                @click="emit('previousPage')"
            >
                ‹
            </button>
            <template v-for="page in getVisiblePages()" :key="page">
                <span v-if="page === '...'" class="px-3 py-1 text-gray-500"> ... </span>
                <button
                    v-else
                    :class="[
                        'px-3 py-1 border rounded text-sm transition-colors',
                        page === meta.currentPage
                            ? 'bg-orange-600 text-white border-orange-600 hover:bg-orange-700'
                            : 'border-gray-300 hover:bg-orange-50 hover:border-orange-300',
                    ]"
                    @click="emit('goToPage', page as number)"
                >
                    {{ page }}
                </button>
            </template>
            <button
                :disabled="!meta.hasNextPage"
                class="px-3 py-1 border border-gray-300 rounded text-sm disabled:opacity-50 disabled:cursor-not-allowed hover:bg-orange-50 hover:border-orange-300 transition-colors"
                @click="emit('nextPage')"
            >
                ›
            </button>
            <button
                :disabled="!meta.hasNextPage"
                class="px-3 py-1 border border-gray-300 rounded text-sm disabled:opacity-50 disabled:cursor-not-allowed hover:bg-orange-50 hover:border-orange-300 transition-colors"
                @click="emit('lastPage')"
            >
                »»
            </button>
        </div>
    </div>
</template>
