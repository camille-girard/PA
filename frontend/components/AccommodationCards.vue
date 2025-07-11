<script setup lang="ts">
    import type { AccommodationItemDto } from '~/types/dtos/accommodation_item.dto';

    const _props = withDefaults(
        defineProps<{
            items?: AccommodationItemDto[];
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
                <p class="text-h6 font-semibold text-gray-900 mb-1">{{ item.title }}</p>
                <p v-if="typeof item.price === 'number'" class="text-orange-600 font-bold text-base">
                    {{ item.price.toFixed(2) }} €
                    <span class="text-sm text-gray-500 font-medium">/ nuit</span>
                </p>
            </div>
        </NuxtLink>
    </div>
</template>
