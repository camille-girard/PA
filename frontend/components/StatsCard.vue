<template>
    <div class="bg-white rounded-[20px] shadow-custom p-6 flex items-center justify-center overflow-hidden w-fit">
        <template v-for="(stat, index) in stats" :key="index">
            <!-- Stat -->
            <div class="flex items-center gap-6 w-60">
                <!-- Icon with gradient background -->
                <div class="icon-circle w-20 h-20 flex items-center justify-center rounded-full">
                    <component :is="stat.icon" class="w-9 h-9 text-brand-600" />
                </div>

                <!-- Label and value -->
                <div class="space-y-1">
                    <p class="text-sm text-normal text-gray-400">{{ stat.label }}</p>
                    <p class="text-3xl font-semibold text-gray-900">{{ stat.value }}</p>
                </div>
            </div>

            <!-- Divider -->
            <div v-if="index !== stats.length - 1" class="h-20 w-px bg-gray-200 mr-10"></div>
        </template>
    </div>
</template>

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

<style scoped>
    .shadow-custom {
        box-shadow: 0px 10px 60px rgba(226, 236, 249, 0.5);
    }

    .icon-circle {
        background: linear-gradient(201deg, #ffd2b9 3.14%, #ffe8db 86.04%);
    }
</style>
