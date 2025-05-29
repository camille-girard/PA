<script setup>
import { ref, computed } from 'vue'
import UDatePicker from '~/components/molecules/UDatePicker.vue'
import UInputNumber from '~/components/atoms/UInputNumber.vue'

const pricePerNight = 350

const arrivalDate = ref(null)
const departureDate = ref(null)
const amountTravelers = ref(0)

const numberOfNights = computed(() => {
  if (!arrivalDate.value || !departureDate.value) return 0
  const diff = new Date(departureDate.value).getTime() - new Date(arrivalDate.value).getTime()
  const nights = diff / (1000 * 60 * 60 * 24)
  return nights > 0 ? nights : 0
})

const total = computed(() => numberOfNights.value * pricePerNight)
</script>

<template>
  <div class="w-full max-w-sm p-6 bg-white border border-gray-300 rounded-2xl shadow-sm space-y-4">
    <div class="text-center">
      <p class="font-bold text-lg">
        {{ pricePerNight }} € <span class="font-normal text-gray-500">/ nuit</span>
      </p>
    </div>
    <UDatePicker v-model="arrivalDate" placeholder="Arrivée" type="date" class="w-full" />
    <UDatePicker v-model="departureDate" placeholder="Départ" type="date" class="w-full" />
    <UInputNumber
        v-model="amountTravelers"
        placeholder="Nombre de voyageur"
        :min="0"
        class="w-full"
        suffix="voyageur"
    />
    <div class="w-full rounded-md border border-gray-200 bg-gray-50 px-4 py-3 text-center">
      <p class="text-sm text-gray-500">Total du séjour</p>
      <p class="text-lg font-semibold text-gray-800">{{ total > 0 ? `${total} €` : '—' }}</p>
    </div>
    <div class="flex space-x-2 pt-2">
      <button class="w-1/2 bg-orange-500 hover:bg-orange-600 text-white font-semibold py-2 rounded-md">
        Réserver
      </button>
      <button class="w-1/2 bg-black hover:bg-gray-800 text-white font-semibold py-2 rounded-md">
        Contacter l'hôte
      </button>
    </div>
  </div>
</template>
