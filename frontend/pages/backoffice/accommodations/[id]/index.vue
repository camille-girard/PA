<script setup lang="ts">
    import { useRoute } from 'vue-router';
    import { ref, computed, onMounted } from 'vue';
    import UCard from '~/components/molecules/UCard.vue';
    import UBadge from '~/components/atoms/UBadge.vue';
    import UTable from '~/components/organisms/UTable.vue';
    import { useRuntimeConfig } from '#app';
    import { useAuthFetch } from '~/composables/useAuthFetch';
    import { useToast } from '~/composables/useToast';
    import type { Accommodation } from '~/types/accommodation';
    import type { Booking } from '~/types/booking';
    import type { ApiError } from '~/types/apiError';

    definePageMeta({
        layout: 'backoffice',
        middleware: 'admin',
    });

    const {
        public: { apiUrl },
    } = useRuntimeConfig();
    const route = useRoute();
    const id = route.params.id as string;

    const pending = ref(false);
    const accommodation = ref<Accommodation | null>(null);

    const showImageModal = ref(false);
    const selectedImageUrl = ref('');
    const toast = useToast();

    async function loadAccommodation() {
        if (!id) {
            toast.error('Erreur', "ID manquant dans l'URL.");
            return;
        }

        pending.value = true;
        try {
            const { data } = await useAuthFetch(`/api/accommodations/${id}`, {
                baseURL: apiUrl,
            });
            accommodation.value = data.value ?? null;
        } catch (error: unknown) {
            const err = error as ApiError;
            console.error(err);
            toast.error('Erreur', err?.data?.message || err?.message || 'Erreur lors du chargement du logement.');
        } finally {
            pending.value = false;
        }
    }

    onMounted(() => {
        loadAccommodation();
    });

    const bookings = computed(
        () =>
            accommodation.value?.bookings?.map((b: Booking) => ({
                id: b.id,
                client: `${b.client?.firstName ?? ''} ${b.client?.lastName ?? ''}`,
                startDate: new Date(b.startDate).toLocaleDateString(),
                endDate: new Date(b.endDate).toLocaleDateString(),
                status: b.status.toLowerCase(),
            })) ?? []
    );

    const columns = [
        { key: 'client', label: 'Client' },
        { key: 'startDate', label: 'Début' },
        { key: 'endDate', label: 'Fin' },
        { key: 'status', label: 'Statut' },
    ];

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

    const isOccupiedNow = computed(() => {
        if (!accommodation.value?.bookings) return false;
        const today = new Date();
        return accommodation.value.bookings.some((b: Booking) => {
            const start = new Date(b.startDate);
            const end = new Date(b.endDate);
            return start <= today && today <= end;
        });
    });

    function openImageModal(url: string) {
        selectedImageUrl.value = url;
        showImageModal.value = true;
    }
</script>

<template>
    <div>
        <div class="space-y-8 max-w-6xl mx-auto">
            <h1 class="text-2xl font-semibold">Détail de l'hébergement</h1>

            <div v-if="accommodation" class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <UCard>
                    <div class="space-y-4">
                        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                            <h2 class="text-lg font-semibold text-brand-500">{{ accommodation.name }}</h2>
                            <UBadge :color="isOccupiedNow ? 'warning' : 'success'" variant="pill">
                                {{ isOccupiedNow ? 'Occupé' : 'Disponible' }}
                            </UBadge>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                            <div>
                                <strong>Adresse :</strong> {{ accommodation.address }},
                                {{ accommodation.city ?? 'N/A' }}
                            </div>
                            <div><strong>Code postal :</strong> {{ accommodation.postalCode ?? 'N/A' }}</div>
                            <div><strong>Pays :</strong> {{ accommodation.country ?? 'France' }}</div>
                            <div><strong>Type :</strong> {{ accommodation.type ?? 'Non renseigné' }}</div>
                            <div><strong>Prix / nuit :</strong> {{ accommodation.price.toFixed(2) }} €</div>
                            <div><strong>Capacité :</strong> {{ accommodation.capacity }} personnes</div>
                            <div><strong>Chambres :</strong> {{ accommodation.bedrooms }}</div>
                            <div><strong>Salles de bain :</strong> {{ accommodation.bathrooms }}</div>
                            <div class="sm:col-span-2">
                                <strong>Propriétaire :</strong> {{ accommodation.host?.firstName }}
                                {{ accommodation.host?.lastName }}
                            </div>
                            <div><strong>Thème :</strong> {{ accommodation.theme ?? 'Aucun' }}</div>
                        </div>

                        <div v-if="accommodation.description" class="pt-2 border-t">
                            <strong>Description :</strong>
                            <p class="text-gray-700 mt-1 whitespace-pre-wrap">{{ accommodation.description }}</p>
                        </div>

                        <div v-if="accommodation.practicalInformations" class="pt-2 border-t">
                            <strong>Informations pratiques :</strong>
                            <p class="text-gray-700 mt-1 whitespace-pre-wrap">
                                {{ accommodation.practicalInformations }}
                            </p>
                        </div>

                        <div v-if="accommodation.advantage?.length" class="pt-2 border-t">
                            <strong>Avantages :</strong>
                            <ul class="list-disc list-inside text-gray-700 mt-1">
                                <li v-for="(adv, index) in accommodation.advantage" :key="index">
                                    {{ adv }}
                                </li>
                            </ul>
                        </div>

                        <div class="pt-2 border-t">
                            <strong>Coordonnées GPS :</strong>
                            <p class="text-gray-700 mt-1">
                                Latitude : {{ accommodation.latitude ?? 'N/A' }}, Longitude :
                                {{ accommodation.longitude ?? 'N/A' }}
                            </p>
                        </div>

                        <div v-if="accommodation.images?.length" class="pt-2 border-t">
                            <strong>Images :</strong>
                            <div class="flex flex-wrap gap-3 mt-2">
                                <img
                                    v-for="(img, index) in accommodation.images"
                                    :key="index"
                                    :src="img.url"
                                    :alt="img.alt"
                                    class="h-32 w-48 object-cover rounded border cursor-pointer hover:opacity-80 transition"
                                    @click="openImageModal(img.url)"
                                />
                            </div>
                        </div>
                    </div>
                </UCard>

                <div>
                    <UCard>
                        <template #header>
                            <h2 class="text-xl font-semibold">Réservations associées</h2>
                        </template>

                        <div v-if="bookings.length">
                            <UTable :columns="columns" :data="bookings">
                                <template #cell-status="{ value }">
                                    <UBadge
                                        size="sm"
                                        variant="pill"
                                        class="text-center"
                                        :color="getStatusProps(value).color"
                                    >
                                        {{ getStatusProps(value).label }}
                                    </UBadge>
                                </template>
                            </UTable>
                        </div>
                        <div v-else class="text-gray-500 text-sm py-4 text-center">
                            Aucune réservation pour ce logement.
                        </div>
                    </UCard>
                </div>
            </div>
        </div>

        <div
            v-if="showImageModal"
            class="fixed inset-0 bg-black bg-opacity-80 flex items-center justify-center z-50"
            @click.self="showImageModal = false"
        >
            <img :src="selectedImageUrl" alt="Image logement" class="max-h-[90vh] max-w-[90vw] rounded shadow-lg" />
            <button class="absolute top-4 right-4 text-white text-3xl font-bold" @click="showImageModal = false">
                &times;
            </button>
        </div>
    </div>
</template>
