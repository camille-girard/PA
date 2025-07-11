<script setup lang="ts">
    import { useRoute } from 'vue-router';
    import { ref, computed } from 'vue';
    import UCard from '~/components/molecules/UCard.vue';
    import UBadge from '~/components/atoms/UBadge.vue';
    import UTable from '~/components/organisms/UTable.vue';
    import UButton from '~/components/atoms/UButton.vue';
    import BaseModal from '~/components/BaseModal.vue';
    import { useRuntimeConfig } from '#app';
    import { useAuthFetch } from '~/composables/useAuthFetch';

    interface Booking {
        id: string;
        startDate: string;
        endDate: string;
        status: string;
    }

    interface Accommodation {
        id: string;
        name: string;
        address: string;
        capacity: number;
        price: number;
        bookings: Booking[];
    }

    interface Owner {
        id: string;
        firstName: string;
        lastName: string;
        email: string;
        phone?: string;
        isVerified: boolean;
        accommodationCount?: number;
        notation?: number;
        accommodations?: Accommodation[];
    }

    definePageMeta({
        layout: 'backoffice',
        middleware: 'admin',
    });

    const {
        public: { apiUrl },
    } = useRuntimeConfig();
    const route = useRoute();
    const id = route.params.id;

    const owner = ref<Owner | null>(null);
    const pending = ref(true);

    onMounted(async () => {
        pending.value = true;
        const { data } = await useAuthFetch<Owner>(`/api/owners/${id}`, {
            baseURL: apiUrl,
        });
        owner.value = data.value ?? null;
        pending.value = false;
    });

    const selectedAccommodation = ref<Accommodation | null>(null);
    const showModal = ref(false);

    function openAvailability(accommodation: Accommodation) {
        selectedAccommodation.value = accommodation;
        showModal.value = true;
    }

    interface AccommodationTableRow {
        id: string;
        name: string;
        address: string;
        capacity: number;
        price: string;
        status: 'Occupé' | 'Disponible';
        bookings: Booking[];
    }

    const accommodations = computed(
        () =>
            owner.value?.accommodations?.map(
                (a): AccommodationTableRow => ({
                    id: a.id,
                    name: a.name,
                    address: a.address,
                    capacity: a.capacity,
                    price: `${a.price.toFixed(2)} €`,
                    status: a.bookings?.some((b) => {
                        const now = new Date();
                        return new Date(b.startDate) <= now && new Date(b.endDate) >= now;
                    })
                        ? 'Occupé'
                        : 'Disponible',
                    bookings: a.bookings,
                })
            ) ?? []
    );

    const columns = [
        { key: 'name', label: 'Nom du logement' },
        { key: 'address', label: 'Adresse' },
        { key: 'capacity', label: 'Capacité' },
        { key: 'price', label: 'Prix' },
        { key: 'status', label: 'Disponibilité' },
        { key: 'actions', label: '' },
    ];

    const getStatusProps = (status: string) =>
        status === 'Occupé' ? { label: 'Occupé', color: 'warning' } : { label: 'Disponible', color: 'success' };
</script>

<template>
    <div class="space-y-6">
        <h1 class="text-2xl font-semibold">Fiche hôte</h1>

        <UCard>
            <template #header>
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center">
                    <div class="text-lg font-medium">{{ owner?.firstName }} {{ owner?.lastName }}</div>
                    <UBadge :color="owner?.isVerified ? 'success' : 'error'" variant="pill">
                        {{ owner?.isVerified ? 'Vérifié' : 'Non vérifié' }}
                    </UBadge>
                </div>
            </template>

            <div class="grid md:grid-cols-2 gap-6">
                <div><strong>Email :</strong> {{ owner?.email }}</div>
                <div><strong>Téléphone :</strong> {{ owner?.phone || 'Non renseigné' }}</div>
                <div><strong>Nombre de logements :</strong> {{ owner?.accommodationCount }}</div>
                <div><strong>Note :</strong> {{ owner?.notation?.toFixed(1) }}/5</div>
            </div>
        </UCard>

        <div>
            <h2 class="text-lg font-semibold mb-4">Hébergements</h2>

            <UTable :columns="columns" :data="accommodations">
                <template #cell-status="{ value }">
                    <UBadge size="sm" variant="pill" :color="getStatusProps(value).color">
                        {{ getStatusProps(value).label }}
                    </UBadge>
                </template>

                <template #cell-actions="{ row }">
                    <UButton class="bg-brand-500" size="sm" @click="openAvailability(row)">
                        Voir disponibilités
                    </UButton>
                </template>
            </UTable>
        </div>

        <!-- Modale avec les périodes de réservation -->
        <BaseModal v-model="showModal" title="Disponibilités de l’hébergement">
            <template #default>
                <div class="space-y-3">
                    <p class="font-medium">{{ selectedAccommodation?.name }}</p>
                    <div v-if="selectedAccommodation?.bookings?.length">
                        <ul class="text-sm space-y-1">
                            <li v-for="booking in selectedAccommodation.bookings" :key="booking.id">
                                Du {{ new Date(booking.startDate).toLocaleDateString() }} au
                                {{ new Date(booking.endDate).toLocaleDateString() }} —
                                <strong>{{ booking.status }}</strong>
                            </li>
                        </ul>
                    </div>
                    <div v-else class="text-gray-500 text-sm">Aucun créneau réservé pour ce logement.</div>
                </div>
            </template>
        </BaseModal>
    </div>
</template>
