<script setup>
import { ref } from 'vue'

const arrival = ref('')
const departure = ref('')
const guests = ref('')
const total = ref('')
</script>

<template>
  <div class="w-full max-w-sm p-6 bg-white border border-gray-300 rounded-2xl shadow-sm space-y-4">
    <div class="text-center">
      <p class="font-bold text-lg">350 € <span class="font-normal text-gray-500">/ nuit</span></p>
    </div>
    <UDatePicker v-model="arrivalDate" placeholder="Arrivée" type="date" class="w-full" />
    <UDatePicker v-model="departureDate" placeholder="Départ" type="date" class="w-full" />
    <div class="space-y-3">
      <input
          v-model="arrival"
          type="text"
          placeholder="Arrivée"
          class="w-full px-4 py-2 border border-orange-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-400"
      />
      <input
          v-model="departure"
          type="text"
          placeholder="Départ"
          class="w-full px-4 py-2 border border-orange-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-400"
      />
      <select
          v-model="guests"
          class="w-full px-4 py-2 border border-orange-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-400"
      >
        <option disabled value="">Nombre de voyageurs</option>
        <option>1 voyageur</option>
        <option>2 voyageurs</option>
        <option>3 voyageurs</option>
        <option>4 voyageurs</option>
        <option>5 voyageurs</option>
      </select>
      <input
          v-model="total"
          type="text"
          placeholder="Total"
          class="w-full px-4 py-2 border border-orange-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-400"
      />
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
