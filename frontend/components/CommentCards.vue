<script setup>
import { defineProps } from 'vue'

defineProps({
  items: {
    type: Array,
    default: () => []
  }
})
</script>

<template>
  <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div
        v-for="item in items"
        :key="item.id"
        class="p-4 bg-white rounded-2xl shadow-md flex flex-col items-start space-y-2"
    >
      <div class="flex items-center space-x-2">
        <img
            class="w-10 h-10 rounded-full object-cover"
            :src="item.userImage"
            alt="avatar"
        />
        <div>
          <p class="font-semibold text-gray-900">{{ item.name }}</p>
          <p class="text-sm text-gray-500">{{ item.userDetail }}</p>
        </div>
      </div>

      <div class="flex items-center">
        <template v-for="n in item.rating" :key="n">
          <svg
              class="w-5 h-5 text-orange-500"
              fill="currentColor"
              viewBox="0 0 20 20"
          >
            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.293c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.176 0l-2.8 2.034c-.784.57-1.838-.197-1.54-1.118l1.07-3.293a1 1 0 00-.364-1.118L2.98 8.719c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
          </svg>
        </template>
      </div>

      <p class="text-gray-700 text-base">
        {{ item.comment }}
      </p>
    </div>
  </div>
</template>
