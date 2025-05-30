<script setup lang="ts">

import CoinsHandIcon from "~/components/atoms/icons/CoinsHandIcon.vue";
import StatsCard from "~/components/StatsCard.vue";
import UTable from '~/components/organisms/UTable.vue';
import UBadge from '~/components/atoms/UBadge.vue';
import PinIcon from "~/components/atoms/icons/PinIcon.vue";
import InfoCircleIcon from "~/components/atoms/icons/InfoCircleIcon.vue";

const { data: clientData, pending, error } = await useFetch('/api/clients', {
  baseURL: 'http://localhost',
})

const clients = computed(() => clientData.value || [])

definePageMeta({
  layout: 'backoffice',
})

const columns = [
  { key: 'client', label: 'Client', sortable: true },
  { key: 'phone', label: 'Téléphone' },
  { key: 'email', label: 'Email' },
  { key: 'status', label: 'Status' },
  { key: 'actions', label: '' }
]

const clientsData = computed(() =>
    clients.value.map(client => ({
      client: `${client.firstName} ${client.lastName}`,
      phone: client.phone || 'Non renseigné',
      email: client.email,
      status: client.isVerified ? 'active' : 'inactive',
    }))
)


function getStatusProps(status: string) {
  switch (status.toLowerCase()) {
    case 'active':
      return { label: 'Active', color: 'success' }
    case 'inactive':
      return { label: 'Inactive', color: 'error' }
    case 'pending':
    case 'en attente':
      return { label: 'En attente', color: 'warning' }
    default:
      return { label: status, color: 'brand' }
  }
}

</script>

<template>

  <p class="text-2xl font-semibold">Clients</p>


  <div class="flex items-center justify-between mb-6">
    <h1 class="text-xl font-semibold">Tous les clients</h1>
    <UBadge size="sm" color="brand">100</UBadge>
  </div>

  <UTable :columns="columns" :data="clientsData">
    <!-- status -->
    <template #cell-status="{ value }">
      <UBadge size="sm" variant="pill" :color="getStatusProps(value).color" class="w-fit">
        {{ getStatusProps(value).label }}
      </UBadge>
    </template>

    <!-- actions -->
    <template #cell-actions>
      <div class="flex items-center gap-2">
        <button class="text-error hover:text-error-dark">
          <InfoCircleIcon class="w-4 h-4" />
        </button>
        <button class="text-gray-400 hover:text-gray-700">
          <PinIcon class="w-4 h-4" />
        </button>
      </div>
    </template>
  </UTable>


</template>


