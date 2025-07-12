<script setup lang="ts">
    import { useRoute } from 'vue-router';
    import { ref, computed, onMounted } from 'vue';
    import { useRuntimeConfig } from '#app';
    import { useAuthFetch } from '~/composables/useAuthFetch';
    import { useToast } from '~/composables/useToast';
    import type { OwnerDto } from '~/types/dtos/owner.dto';

    import UCard from '~/components/molecules/UCard.vue';
    import UBadge from '~/components/atoms/UBadge.vue';
    import UTable from '~/components/organisms/UTable.vue';
    import UButton from '~/components/atoms/UButton.vue';
    import BaseModal from '~/components/BaseModal.vue';

    definePageMeta({
        layout: 'backoffice',
        middleware: 'admin',
    });
    const toast = useToast();
    const route = useRoute();
    const {
        public: { apiUrl },
    } = useRuntimeConfig();

    const id = route.params.id;
    const owner = ref<OwnerDto | null>(null);
    const pending = ref(true);

    onMounted(async () => {
        try {
            pending.value = true;
            const { data, error } = await useAuthFetch<OwnerDto>(`/api/owners/${id}`, { baseURL: apiUrl });
            if (error.value) throw error.value;
            owner.value = data.value ?? null;
            if (!owner.value) toast.error('Erreur', 'Hôte non trouvé.');
        } catch (err) {
            toast.error('Erreur', extractErrorMessage(err));
        } finally {
            pending.value = false;
        }
    });

    function extractErrorMessage(err: unknown): string {
        if (typeof err === 'object' && err !== null) {
            const e = err as { data?: { message?: string; error?: string }; message?: string };
            if (e.data?.error) return e.data.error;
            if (e.data?.message) return e.data.message;
            if (e.message) return e.message;
        }
        return 'Erreur inattendue';
    }

    const selectedAccommodation = ref<OwnerDto['accommodations'][0] | null>(null);
    const showModal = ref(false);

    function openAvailability(accommodation: OwnerDto['accommodations'][0]) {
        selectedAccommodation.value = accommodation;
        showModal.value = true;
    }

    const accommodations = computed(
        () =>
            owner.value?.accommodations?.map((a) => ({
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
            })) ?? []
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

        <div v-if="pending" class="text-gray-600">Chargement…</div>
        <div v-else-if="!owner" class="text-red-600">Hôte non trouvé.</div>
        <div v-else>
            <UCard>
                <template #header>
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-6">
                        <div class="flex items-center gap-6">
                            <img
                                v-if="owner.avatar"
                                :src="owner.avatar"
                                alt="Avatar hôte"
                                class="w-32 h-32 rounded-full object-cover border shadow"
                            />
                            <div>
                                <div class="text-xl font-semibold">{{ owner.firstName }} {{ owner.lastName }}</div>
                                <div class="text-gray-500">{{ owner.email }}</div>
                            </div>
                        </div>
                        <UBadge :color="owner.isVerified ? 'success' : 'error'" variant="pill">
                            {{ owner.isVerified ? 'Vérifié' : 'Non vérifié' }}
                        </UBadge>
                    </div>
                </template>

                <div class="grid md:grid-cols-2 gap-6 mt-4">
                    <div><strong>Prénom :</strong> {{ owner.firstName }}</div>
                    <div><strong>Nom :</strong> {{ owner.lastName }}</div>
                    <div><strong>Email :</strong> {{ owner.email }}</div>
                    <div><strong>Téléphone :</strong> {{ owner.phone || 'Non renseigné' }}</div>
                    <div><strong>Adresse :</strong> {{ owner.address || 'Non renseignée' }}</div>
                    <div>
                        <strong>Date de création :</strong>
                        <span v-if="owner.createdAt">{{ new Date(owner.createdAt).toLocaleDateString() }}</span>
                        <span v-else>Non disponible</span>
                    </div>
                    <div><strong>Bio :</strong> {{ owner.bio || 'Non renseignée' }}</div>
                    <div><strong>Nombre de logements :</strong> {{ owner.accommodationCount ?? 0 }}</div>
                    <div><strong>Note :</strong> {{ owner.notation?.toFixed(1) ?? '0.0' }}/5</div>
                </div>
            </UCard>

            <div>
                <h2 class="text-lg font-semibold mb-4 mt-8">Hébergements</h2>
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
    </div>
</template>
