<script setup lang="ts">
    interface TrendingItem {
        title: string;
        image: string;
    }

    const _props = defineProps<{
        items: TrendingItem[];
    }>();
</script>

<template>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
        <div
            v-for="item in items"
            :key="item.title"
            class="rounded-xl overflow-hidden shadow hover:shadow-lg transition"
        >
            <img :src="item.image" :alt="item.title" class="w-full h-56 object-cover" />
            <div class="px-4 py-3">
                <p class="text-h6">{{ item.title }}</p>
            </div>
        </div>
    </div>
</template>
