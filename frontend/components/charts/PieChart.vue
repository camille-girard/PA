<template>
    <Pie :data="chartData" :options="chartOptions" />
</template>

<script setup lang="ts">
    import { computed } from 'vue';
    import { Pie } from 'vue-chartjs';
    import { Chart as ChartJS, Tooltip, Legend, ArcElement } from 'chart.js';

    ChartJS.register(Tooltip, Legend, ArcElement);

    const props = defineProps<{ data: Record<string, number> }>();

    const chartData = computed(() => ({
        labels: Object.keys(props.data),
        datasets: [
            {
                data: Object.values(props.data),
                backgroundColor: ['#36A2EB', '#FFCE56', '#FF6384'],
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
