<script setup lang="ts">
    import { ref, computed, onMounted } from 'vue';
    import UTable from '~/components/organisms/UTable.vue';
    import UBadge from '~/components/atoms/UBadge.vue';
    import TrashIcon from '~/components/atoms/icons/TrashIcon.vue';
    import ConfirmPopover from '~/components/ConfirmPopover.vue';
    import { useRuntimeConfig } from '#app';
    import { useAuthFetch } from '~/composables/useAuthFetch';
    import { useToast } from '~/composables/useToast';
    import type { Booking } from '~/types/booking';

    definePageMeta({
        layout: 'backoffice',
        middleware: 'admin',
    });

    const {
        public: { apiUrl },
    } = useRuntimeConfig();

    const toast = useToast();

    const bookingData = ref<Booking[]>([]);
    const pending = ref(false);

    const columns = [
        { key: 'client', label: 'Client', sortable: true },
        { key: 'owner', label: 'Hôte' },
        { key: 'accommodation', label: 'Hébergement' },
        { key: 'address', label: 'Adresse' },
        { key: 'startDate', label: 'Début' },
        { key: 'endDate', label: 'Fin' },
        { key: 'totalPrice', label: 'Prix' },
        { key: 'status', label: 'Statut' },
        { key: 'actions', label: '' },
    ];

    const bookings = computed(() => bookingData.value || []);

    const bookingsData = computed(() =>
        bookings.value.map((b) => ({
            id: b.id,
            client: `${b.client?.firstName || ''} ${b.client?.lastName || ''}`,
            owner: `${b.accommodation?.owner?.firstName || ''} ${b.accommodation?.owner?.lastName || ''}`,
            accommodation: b.accommodation?.name,
            address: b.accommodation?.address,
            startDate: new Date(b.startDate).toLocaleDateString(),
            endDate: new Date(b.endDate).toLocaleDateString(),
            totalPrice: `${b.totalPrice.toFixed(2)} €`,
            status: b.status?.toLowerCase() ?? '',
        }))
    );

    function getStatusProps(status: string) {
        switch (status) {
            case 'accepted':
                return { label: 'Acceptée', color: 'success' };
            case 'pending':
                return { label: 'En attente', color: 'warning' };
            case 'refused':
                return { label: 'Refusée', color: 'error' };
            default:
                return { label: status, color: 'gray' };
        }
    }

    async function loadBookings() {
        pending.value = true;
        try {
            const { data } = await useAuthFetch('/api/bookings', { baseURL: apiUrl });
            bookingData.value = data.value || [];
        } catch (error: unknown) {
            toast.error('Erreur', error?.data?.message || 'Erreur lors du chargement des réservations.');
            console.error(error);
        } finally {
            pending.value = false;
        }
    }

    async function refreshBookings() {
        await loadBookings();
    }

    async function deleteBooking(id: number) {
        pending.value = true;
        try {
            await $fetch(`/api/bookings/${id}`, {
                method: 'DELETE',
                baseURL: apiUrl,
            });
            await refreshBookings();
            toast.success('Succès', 'Réservation supprimée avec succès.');
        } catch (error: unknown) {
            toast.error('Erreur', error?.data?.message || 'Erreur lors de la suppression.');
            console.error(error);
        } finally {
            pending.value = false;
        }
    }

    onMounted(() => {
        loadBookings();
    });
</script>

<template>
    <div class="space-y-6">
        <h2 class="text-2xl font-semibold flex items-center gap-2">Réservations
            <UBadge variant="pill" color="brand" size="md">
                {{ bookings.length }}
            </UBadge>
        </h2>

        <div v-if="pending" class="text-gray-600">Chargement…</div>

        <div v-else>
            <div class="w-full overflow-x-auto">
                <div class="min-w-[800px]">
                    <UTable :columns="columns" :data="bookingsData">
                        <template #cell-status="{ value }">
                            <UBadge size="sm" variant="pill" :color="getStatusProps(value).color" class="w-max">
                                {{ getStatusProps(value).label }}
                            </UBadge>
                        </template>

                        <template #cell-actions="{ row }">
                            <div class="flex items-center gap-4">
                                <ConfirmPopover
                                    :item-name="`la réservation de ${row.client}`"
                                    @confirm="deleteBooking(row.id)"
                                >
                                    <template #trigger>
                                        <button class="text-red-500 hover:text-red-700">
                                            <TrashIcon class="w-6 h-6" />
                                        </button>
                                    </template>
                                </ConfirmPopover>
                            </div>
                        </template>
                    </UTable>
                </div>
            </div>
        </div>
    </div>
</template>
