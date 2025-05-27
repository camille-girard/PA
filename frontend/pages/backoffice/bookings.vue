<script setup lang="ts">
    import CoinsHandIcon from '~/components/atoms/icons/CoinsHandIcon.vue';
    import StatsCard from '~/components/StatsCard.vue';
    import UTable from '~/components/organisms/UTable.vue';
    import UBadge from '~/components/atoms/UBadge.vue';
    import PinIcon from '~/components/atoms/icons/PinIcon.vue';
    import InfoCircleIcon from '~/components/atoms/icons/InfoCircleIcon.vue';

    definePageMeta({
        layout: 'backoffice',
    });

    const stats = [{ label: 'Réservations', value: '2,345', icon: CoinsHandIcon }];

    const columns = [
        { key: 'client', label: 'Client', sortable: true },
        { key: 'host', label: 'Hôte', sortable: true },
        { key: 'reference', label: 'Référence' },
        { key: 'country', label: 'Pays' },
        { key: 'email', label: 'Email address' },
        { key: 'status', label: 'Status' },
        { key: 'actions', label: '', width: 'w-16' },
    ];

    const reservations = [
        {
            client: 'Olivia Rhye',
            host: 'Andi Lane',
            reference: 'RS29372',
            country: 'France',
            email: 'olivia@untitledui.com',
            status: 'active',
        },
        {
            client: 'Phoenix Baker',
            host: 'Demi Wilkinson',
            reference: 'RS29372',
            country: 'USA',
            email: 'phoenix@untitledui.com',
            status: 'inactive',
        },
        {
            client: 'Lana Steiner',
            host: 'Phoenix Baker',
            reference: 'RS29372',
            country: 'Moldavie',
            email: 'lana@untitledui.com',
            status: 'active',
        },
        {
            client: 'Demi Wilkinson',
            host: 'Candice Wu',
            reference: 'RS29372',
            country: 'China',
            email: 'demi@untitledui.com',
            status: 'active',
        },
        {
            client: 'Candice Wu',
            host: 'Candice Wu',
            reference: 'RS29372',
            country: 'France',
            email: 'candice@untitledui.com',
            status: 'active',
        },
        {
            client: 'Natali Craig',
            host: 'Demi Wilkinson',
            reference: 'RS29372',
            country: 'Italie',
            email: 'natali@untitledui.com',
            status: 'inactive',
        },
        {
            client: 'Candice Wu',
            host: 'Demi Wilkinson',
            reference: 'RS29372',
            country: 'Italie',
            email: 'drew@untitledui.com',
            status: 'active',
        },
        {
            client: 'Orlando Diggs',
            host: 'Candice Wu',
            reference: 'RS29372',
            country: 'France',
            email: 'orlando@untitledui.com',
            status: 'active',
        },
        {
            client: 'Andi Lane',
            host: 'Demi Wilkinson',
            reference: 'RS29372',
            country: 'China',
            email: 'andi@untitledui.com',
            status: 'pending',
        },
        {
            client: 'Kate Morrison',
            host: 'Demi Wilkinson',
            reference: 'RS29372',
            country: 'Moldavie',
            email: 'kate@untitledui.com',
            status: 'active',
        },
    ];

    function getStatusProps(status: string) {
        switch (status.toLowerCase()) {
            case 'active':
                return { label: 'Active', color: 'success' };
            case 'inactive':
                return { label: 'Inactive', color: 'error' };
            case 'pending':
            case 'en attente':
                return { label: 'En attente', color: 'warning' };
            default:
                return { label: status, color: 'brand' };
        }
    }
</script>

<template>
    <p class="text-2xl font-semibold">Bookings</p>

    <StatsCard :stats="stats" />

    <div class="flex items-center justify-between mb-6">
        <h1 class="text-xl font-semibold">Toutes les réservations</h1>
        <UBadge size="sm" color="brand">100</UBadge>
    </div>

    <UTable :columns="columns" :data="reservations">
        <!-- Slot status -->
        <template #cell-status="{ value }">
            <UBadge size="sm" variant="pill" :color="getStatusProps(value).color">
                {{ getStatusProps(value).label }}
            </UBadge>
        </template>

        <!-- Slot actions -->
        <template #cell-actions>
            <div class="flex items-center gap-2">
                <button class="text-error hover:text-error-dark">
                    <InfoCircleIcon class="w-4 h-4" />
                </button>
                <button class="text-gray-400 hover:text-gray-700">
                    <PinIcon class="w-4 h-4" />
                </button>
            </div>
        </template>
    </UTable>
</template>
