<script setup lang="ts">
  const props = defineProps<{
    itemName?: string
    title?: string
    message?: string
  }>()

  const emit = defineEmits(['confirm'])
  const open = ref(false)

  function confirm() {
    emit('confirm')
    open.value = false
  }
</script>

<template>
  <div>
    <div @click="open = true">
      <slot name="trigger" />
    </div>

    <div v-if="open" class="fixed inset-0 z-50 flex items-center justify-center">

      <div
          class="absolute inset-0 bg-black bg-opacity-30 backdrop-blur-sm"
          @click="open = false"
      />

      <div class="relative z-10 bg-white dark:bg-gray-900 p-6 rounded-xl shadow-xl w-full max-w-md">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
          {{ title || 'Confirmer la suppression ?' }}
        </h2>
        <p class="text-sm text-gray-700 dark:text-gray-300 mb-4">
          {{ message || `Êtes-vous sûr de vouloir supprimer ${itemName} ?` }}
        </p>
        <div class="flex justify-end gap-2">
          <button
              class="px-3 py-1 text-sm bg-gray-200 dark:bg-gray-700 rounded hover:bg-gray-300"
              @click="open = false"
          >
            Annuler
          </button>
          <button
              class="px-3 py-1 text-sm bg-red-600 text-white rounded hover:bg-red-700"
              @click="confirm"
          >
            Supprimer
          </button>
        </div>
      </div>
    </div>
  </div>
</template>
