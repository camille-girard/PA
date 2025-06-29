<script setup lang="ts">
    import { useRoute } from 'vue-router';
    import { ref, computed } from 'vue';
    import UBadge from '~/components/atoms/UBadge.vue';
    import UCard from '~/components/molecules/UCard.vue';
    import UTable from '~/components/organisms/UTable.vue';

    definePageMeta({ layout: 'backoffice' });

    const route = useRoute();
    const id = route.params.id;
    const {
        public: { apiUrl },
    } = useRuntimeConfig();

    const { data: client, pending } = await useFetch(`/api/clients/${id}`, {
        baseURL: apiUrl,
        transform: (res) => res.client,
    });

    const getStatusProps = (status: boolean) =>
        status ? { label: 'Actif', color: 'success' } : { label: 'Inactif', color: 'error' };

    const bookings = computed(
        () =>
            client.value?.bookings?.map((b: any) => ({
                id: b.id,
                accommodation: b.accommodation?.name ?? '—',
                startDate: new Date(b.startDate).toLocaleDateString(),
                endDate: new Date(b.endDate).toLocaleDateString(),
                status: b.status,
                totalPrice: `${b.totalPrice?.toFixed(2)} €`,
            })) ?? []
    );
</script>

<template>
    <div class="space-y-6">
        <h1 class="text-2xl font-semibold">Fiche client</h1>

        <UCard>
            <template #header>
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center">
                    <div class="text-lg font-medium">{{ client?.firstName }} {{ client?.lastName }}</div>
                    <UBadge :color="getStatusProps(client?.isVerified).color" variant="pill">
                        {{ getStatusProps(client?.isVerified).label }}
                    </UBadge>
                </div>
            </template>

            <div class="grid md:grid-cols-2 gap-6">
                <div><strong>Email :</strong> {{ client?.email }}</div>
                <div><strong>Téléphone :</strong> {{ client?.phone || 'Non renseigné' }}</div>
                <div><strong>Date de création :</strong> {{ new Date(client?.createdAt).toLocaleDateString() }}</div>
                <div><strong>Préférences :</strong> {{ client?.preferences?.join(', ') || '—' }}</div>
            </div>
        </UCard>

        <div>
            <h2 class="text-lg font-semibold mb-4">Réservations</h2>
            <UTable
                :columns="[
                    { key: 'accommodation', label: 'Hébergement' },
                    { key: 'startDate', label: 'Début' },
                    { key: 'endDate', label: 'Fin' },
                    { key: 'status', label: 'Statut' },
                    { key: 'totalPrice', label: 'Prix total' },
                ]"
                :data="bookings"
                :loading="pending"
            />
        </div>
    </div>
</template>
