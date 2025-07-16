<script setup lang="ts">
    import type { Component } from 'vue';

    defineProps<{
        stats: {
            label: string;
            value: string | number;
            icon: Component;
        }[];
    }>();
</script>

<template>
    <div class="py-4 md:p-4 flex flex-wrap md:flex-row gap-4 md:gap-0">
        <div
            v-for="(stat, index) in stats"
            :key="index"
            class="bg-white rounded-[20px] border-white shadow-custom flex items-center gap-4 p-4 flex-1 min-w-[220px] max-w-full md:max-w-none"
        >
            <!-- Icon with gradient background -->
            <div class="icon-circle w-16 h-16 md:w-20 md:h-20 flex items-center justify-center rounded-full">
                <component :is="stat.icon" class="w-7 h-7 md:w-9 md:h-9 text-brand-600" />
            </div>

            <!-- Label and value -->
            <div class="space-y-1">
                <p class="text-sm text-gray-400">{{ stat.label }}</p>
                <p class="text-2xl md:text-3xl font-semibold text-gray-900">{{ stat.value }}</p>
            </div>
        </div>
    </div>
</template>

<style scoped>
    .shadow-custom {
        box-shadow: 0px 10px 60px rgba(226, 236, 249, 0.5);
    }

    .icon-circle {
        background: linear-gradient(201deg, #ffd2b9 3.14%, #ffe8db 86.04%);
    }
</style>
