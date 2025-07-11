<script setup lang="ts">
    import ArrowDownIcon from '~/components/atoms/icons/ArrowDownIcon.vue';
    import ArrowUpIcon from '~/components/atoms/icons/ArrowUpIcon.vue';
    import ChevronSelectorIcon from '~/components/atoms/icons/ChevronSelectorIcon.vue';

    export interface Column {
        key: string;
        label: string;
        sortable?: boolean;
        width?: string;
    }

    // Generic type for table data
    type TableData = Record<string, unknown>;

    interface TableProps {
        columns: Column[];
        data: TableData[];
        loading?: boolean;
        striped?: boolean;
        hoverable?: boolean;
        bordered?: boolean;
        selectable?: boolean;
    }

    const props = withDefaults(defineProps<TableProps>(), {
        loading: false,
        striped: false,
        hoverable: true,
        bordered: false,
        selectable: false,
    });

    const emit = defineEmits<{
        (e: 'row-click', row: TableData): void;
        (e: 'selection-change', selectedRows: TableData[]): void;
    }>();

    // Selected rows state
    const selectedRows = ref<TableData[]>([]);
    const allSelected = ref(false);

    // Check if a row is selected
    const isRowSelected = (row: TableData) => {
        return selectedRows.value.some((selectedRow) => JSON.stringify(selectedRow) === JSON.stringify(row));
    };

    // Toggle selection of a row
    const toggleRowSelection = (row: TableData, _event: Event) => {
        const index = selectedRows.value.findIndex(
            (selectedRow) => JSON.stringify(selectedRow) === JSON.stringify(row)
        );

        if (index === -1) {
            selectedRows.value.push(row);
        } else {
            selectedRows.value.splice(index, 1);
        }

        emit('selection-change', selectedRows.value);
        updateAllSelectedState();
    };

    // Toggle selection of all rows
    const toggleAllRows = () => {
        if (allSelected.value) {
            selectedRows.value = [];
        } else {
            selectedRows.value = [...props.data];
        }

        allSelected.value = !allSelected.value;
        emit('selection-change', selectedRows.value);
    };

    // Update the allSelected state based on the current selection
    const updateAllSelectedState = () => {
        allSelected.value = selectedRows.value.length === props.data.length;
    };

    // Reset selection when data changes
    watch(
        () => props.data,
        () => {
            selectedRows.value = [];
            allSelected.value = false;
        },
        { deep: true }
    );

    // Table styling classes
    const tableClasses = computed(() => ['min-w-full bg-primary border-secondary']);

    const headerClasses = 'bg-secondary border-b border-secondary';

    const headerCellClasses = computed(() => ['px-6 py-3 text-xs font-semibold text-quaternary']);

    const bodyClasses = computed(() => ['bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-800']);

    const rowClasses = computed(() => [
        { 'hover:bg-secondary transition-colors': props.hoverable },
        { 'even:bg-secondary-subtle': props.striped },
    ]);

    const cellClasses = computed(() => ['px-6 py-4 text-sm text-tertiary']);

    // Sort functionality
    const sortColumn = ref<string | null>(null);
    const sortDirection = ref<'asc' | 'desc'>('asc');

    const sortedData = computed(() => {
        if (!sortColumn.value) return props.data;

        return [...props.data].sort((a, b) => {
            const aValue = a[sortColumn.value!];
            const bValue = b[sortColumn.value!];

            if (aValue === bValue) return 0;

            const direction = sortDirection.value === 'asc' ? 1 : -1;

            if (typeof aValue === 'string') {
                return aValue.localeCompare(String(bValue)) * direction;
            }

            // For number comparison
            if (typeof aValue === 'number' && typeof bValue === 'number') {
                return (aValue > bValue ? 1 : -1) * direction;
            }

            // For other types, convert to string and compare
            return String(aValue).localeCompare(String(bValue)) * direction;
        });
    });

    function toggleSort(column: Column) {
        if (!column.sortable) return;

        if (sortColumn.value === column.key) {
            sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc';
        } else {
            sortColumn.value = column.key;
            sortDirection.value = 'asc';
        }
    }

    function getSortIcon(column: Column) {
        if (!column.sortable) return null;

        if (sortColumn.value !== column.key) {
            return ChevronSelectorIcon;
        }

        return sortDirection.value === 'asc' ? ArrowUpIcon : ArrowDownIcon;
    }
</script>

<template>
    <ClientOnly>
        <div class="relative overflow-x-auto rounded-lg border border-secondary">
            <table :class="tableClasses">
                <thead :class="headerClasses">
                    <tr>
                        <th v-if="selectable" class="px-4 py-3 w-8">
                            <UCheckbox
                                name="select-all"
                                :model-value="allSelected"
                                class="mx-auto block"
                                @change="toggleAllRows"
                            />
                        </th>
                        <th
                            v-for="column in columns"
                            :key="column.key"
                            :class="[
                                headerCellClasses,
                                { 'cursor-pointer': column.sortable },
                                column.width ? column.width : 'auto',
                            ]"
                            @click="toggleSort(column)"
                        >
                            <div class="flex items-center gap-1">
                                {{ column.label }}
                                <component :is="getSortIcon(column)" v-if="column.sortable" class="w-4 h-4" />
                            </div>
                        </th>
                    </tr>
                </thead>
                <tbody v-if="!loading && data.length > 0" :class="bodyClasses">
                    <tr
                        v-for="(row, rowIndex) in sortedData"
                        :key="rowIndex"
                        class="cursor-pointer"
                        :class="[rowClasses]"
                        @click="emit('row-click', row)"
                    >
                        <td v-if="selectable" class="px-4 py-4 w-8" @click.stop>
                            <UCheckbox
                                :name="`select-row-${rowIndex}`"
                                :model-value="isRowSelected(row)"
                                class="mx-auto block"
                                @change="toggleRowSelection(row, $event)"
                            />
                        </td>
                        <td v-for="column in columns" :key="column.key" :class="[cellClasses]">
                            <slot :name="`cell-${column.key}`" :row="row" :value="row[column.key]">
                                {{ row[column.key] }}
                            </slot>
                        </td>
                    </tr>
                </tbody>
                <tbody v-else-if="loading">
                    <tr>
                        <td :colspan="selectable ? columns.length + 1 : columns.length" class="px-4 py-8 text-center">
                            <ULoading />
                        </td>
                    </tr>
                </tbody>
                <tbody v-else>
                    <tr>
                        <td
                            :colspan="selectable ? columns.length + 1 : columns.length"
                            class="px-4 py-8 text-center text-gray-500 dark:text-gray-400"
                        >
                            No data available
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </ClientOnly>
</template>
