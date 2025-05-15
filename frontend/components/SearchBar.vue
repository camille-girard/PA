<script setup lang="ts">
import SearchIcon from '~/components/atoms/icons/SearchIcon.vue';

const amountTravelers = ref<number | undefined>(undefined);
const arrivalDate = ref();
const departureDate = ref();
</script>

<template>
  <div>
    <p class="text-lg">
      Trouver et réservez des hébergements uniques à thème cinématographique
    </p>

    <div class="mt-14 max-w-screen-sm space-y-3">
      <UInput placeholder="Destination, thématique" type="text" :icon="SearchIcon" />
      <div class="flex w-full justify-between gap-2">
        <UDatePicker v-model="arrivalDate" placeholder="Arrivée" type="date" class="w-full" />
        <UDatePicker v-model="departureDate" placeholder="Départ" type="date" class="w-full" />
        <UInputNumber
            v-model="amountTravelers"
            placeholder="Voyageurs"
            type="number"
            :min="1"
            class="w-full"
        />
      </div>
    </div>
    <UButton :icon="SearchIcon" icon-position="leading" class="mx-auto mt-8">Rechercher</UButton>

  </div>
</template>