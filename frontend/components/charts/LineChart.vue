<script setup lang="ts">
    import { computed } from 'vue';
    import { Line } from 'vue-chartjs';
    import {
        Chart as ChartJS,
        Title,
        Tooltip,
        Legend,
        LineElement,
        PointElement,
        CategoryScale,
        LinearScale,
        Filler,
    } from 'chart.js';

    ChartJS.register(Title, Tooltip, Legend, LineElement, PointElement, CategoryScale, LinearScale, Filler);

    const props = defineProps<{ monthly: Array<{ month: string; count: number }> }>();

    const chartData = computed(() => ({
        labels: props.monthly.map((item) => item.month),
        datasets: [
            {
                label: 'Réservations par mois',
                data: props.monthly.map((item) => item.count),
                borderColor: '#36A2EB',
                backgroundColor: '#9BD0F5',
                fill: true,
                tension: 0.3,
            },
        ],
    }));

    const chartOptions = {
        responsive: true,
        plugins: {
            legend: { position: 'bottom' },
        },
    };
</script>

<template>
    <Line :data="chartData" :options="chartOptions" />
</template>
