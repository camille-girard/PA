<script setup lang="ts">
    interface TrendingItem {
        title: string;
        image: string;
    }

    const _props = withDefaults(
        defineProps<{
            items?: TrendingItem[];
        }>(),
        {
            items: () => [],
        }
    );
</script>

<template>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
        <NuxtLink
            v-for="item in items"
            :key="item.title"
            :to="`/thematiques/${item.slug}/${item.id}`"
            class="rounded-xl overflow-hidden shadow hover:shadow-lg transition"
        >
            <img :src="item.image" :alt="item.title" class="w-full h-56 object-cover" />
            <div class="px-4 py-3">
                <p class="text-h6">{{ item.title }}</p>
            </div>
        </NuxtLink>
    </div>
</template>
