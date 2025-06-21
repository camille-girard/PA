<script setup lang="ts">
interface TrendingItem {
  title: string
  image: string
  slug: string
  price?: number
}

const _props = withDefaults(
    defineProps<{
      items?: TrendingItem[]
      linkPrefix?: string
    }>(),
    {
      items: () => [],
      linkPrefix: '/accommodations',
    }
)
</script>

<template>
  <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
    <NuxtLink
        v-for="item in items"
        :key="item.slug"
        :to="`${linkPrefix}/${item.slug}${linkPrefix.includes('my-accommodation') ? '/edit' : ''}`"
        class="rounded-xl overflow-hidden shadow hover:shadow-lg transition bg-white"
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
