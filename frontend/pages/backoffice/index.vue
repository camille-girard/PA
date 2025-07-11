<script setup lang="ts">
    import { ref, onMounted } from 'vue';
    import StatsCard from '~/components/StatsCard.vue';
    import UserIcon from '~/components/atoms/icons/UserIcon.vue';
    import BuildingIcon from '~/components/atoms/icons/BuildingIcon.vue';
    import CoinsHandIcon from '~/components/atoms/icons/CoinsHandIcon.vue';
    import PinIcon from '~/components/atoms/icons/PinIcon.vue';
    import LineChart from '~/components/charts/LineChart.vue';
    import PieChart from '~/components/charts/PieChart.vue';
    import { useRuntimeConfig } from '#app';
    import { useAuthFetch } from '~/composables/useAuthFetch';
    import { useToast } from '~/composables/useToast';

    definePageMeta({
        layout: 'backoffice',
        middleware: 'admin',
    });

    const toast = useToast();
    const {
        public: { apiUrl },
    } = useRuntimeConfig();

    const stats = ref([]);
    const bookingStatusData = ref({});
    const monthlyBookings = ref([]);
    const loading = ref(true);

    async function loadStats() {
        try {
            const { data, error } = await useAuthFetch('/api/dashboard/stats', { baseURL: apiUrl });
            if (error.value) {
                toast.error('Erreur', error.value.data?.message || 'Impossible de charger les statistiques.');
                return;
            }
            if (data.value) {
                stats.value = [
                    { label: 'Clients', value: data.value.counts.clients.toLocaleString(), icon: UserIcon },
                    { label: 'Hôtes', value: data.value.counts.owners.toLocaleString(), icon: BuildingIcon },
                    { label: 'Réservations', value: data.value.counts.bookings.toLocaleString(), icon: CoinsHandIcon },
                    { label: 'Hébergements', value: data.value.counts.accommodations.toLocaleString(), icon: PinIcon },
                ];
                bookingStatusData.value = data.value.bookingStatusCounts;
                monthlyBookings.value = data.value.monthlyBookings;
            }
        } catch (e) {
            console.error(e);
            toast.error('Erreur', 'Une erreur est survenue lors du chargement des statistiques.');
        } finally {
            loading.value = false;
        }
    }

    onMounted(() => {
        loadStats();
    });
</script>

<template>
    <div class="space-y-8 sm:space-y-10">
        <p class="text-xl sm:text-2xl font-semibold">Tableau de Bord</p>

        <StatsCard :stats="stats" :loading="loading" />

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 lg:gap-8">
            <div class="bg-white rounded-2xl shadow-sm p-4 sm:p-6 flex flex-col">
                <h2 class="text-lg sm:text-xl font-semibold mb-4">Statuts des réservations</h2>
                <div class="flex-1 flex items-center justify-center">
                    <PieChart
                        v-if="!loading && Object.keys(bookingStatusData).length"
                        :data="bookingStatusData"
                        class="w-full max-w-sm mx-auto"
                    />
                    <p v-else class="text-gray-500 text-center">Chargement…</p>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm p-4 sm:p-6 flex flex-col">
                <h2 class="text-lg sm:text-xl font-semibold mb-4">Réservations par mois</h2>
                <div class="flex-1 flex items-center justify-center">
                    <LineChart
                        v-if="!loading && monthlyBookings.length"
                        :monthly="monthlyBookings"
                        class="w-full max-w-full"
                    />
                    <p v-else class="text-gray-500 text-center">Chargement…</p>
                </div>
            </div>
        </div>
    </div>
</template>
