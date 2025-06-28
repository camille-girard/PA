<script setup lang="ts">
defineProps<{
  booking: any
  contactLabel?: string
  deleteLabel?: string
}>()

const emit = defineEmits(['delete', 'contact'])

const formatDate = (d: Date) =>
    d.toLocaleDateString('fr-FR', {
      day: '2-digit',
      month: 'short',
      year: 'numeric',
    })

function onDelete(id: number) {
  emit('delete', id)
}

function onContact(id: number) {
  emit('contact', id)
}
</script>

<template>
  <div
      class="border border-gray-200 rounded-xl p-6 flex flex-col md:flex-row md:items-center md:justify-between"
  >
    <div class="space-y-1">
      <h3 class="text-xl font-semibold">
        {{ booking.accommodation?.name ?? `Hébergement #${booking.accommodation?.id}` }}
      </h3>
      <p class="text-gray-600">
        Du <strong>{{ formatDate(booking.startDate) }}</strong>
        au <strong>{{ formatDate(booking.endDate) }}</strong>
        ({{ Math.round((booking.endDate - booking.startDate) / 86400000) }} nuits)
      </p>
      <p v-if="booking.client" class="text-sm text-gray-500">
        Par : {{ booking.client.firstName }} {{ booking.client.lastName }}
      </p>
      <div class="flex items-center gap-1">
        <span class="font-medium">Statut :</span>
        <span
            :class="{
            'text-green-600': booking.status === 'accepted',
            'text-yellow-600': booking.status === 'pending',
            'text-red-600': ['cancelled', 'rejected'].includes(booking.status),
          }"
        >
          {{ booking.status }}
        </span>
      </div>
    </div>

    <div class="mt-4 md:mt-0 md:text-right space-y-2">
      <p class="text-lg font-bold">{{ booking.totalPrice.toFixed(2) }} €</p>
      <div class="flex flex-col md:flex-row md:justify-end gap-2">
        <UButton
            size="sm"
            variant="primary"
            class="w-full md:w-auto"
            @click="onContact(booking.id)"
        >
          {{ contactLabel }}
        </UButton>
        <UButton
            size="sm"
            variant="danger"
            class="w-full md:w-auto"
            @click="onDelete(booking.id)"
        >
          {{ deleteLabel }}
        </UButton>
      </div>
    </div>
  </div>
</template>
