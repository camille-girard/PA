<script setup lang="ts">

import UTable from '~/components/organisms/UTable.vue';
import UBadge from '~/components/atoms/UBadge.vue';
import TrashIcon from "~/components/atoms/icons/TrashIcon.vue";
import EditIcon from "~/components/atoms/icons/EditIcon.vue";

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
  { key: 'bookingCount', label: 'Réservations' },
  { key: 'status', label: 'Status' },
  { key: 'actions', label: '' }
]

const clientsData = computed(() =>
    clients.value.map(client => ({
      id: client.id,
      client: `${client.firstName} ${client.lastName}`,
      phone: client.phone || 'Non renseigné',
      email: client.email,
      bookingCount: client.bookingCount ?? 0,
      status: client.isVerified ? 'active' : 'inactive',
      _original: client,
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

  <UTable :columns="columns" :data="clientsData">
    <!-- status -->
    <template #cell-status="{ value }">
      <UBadge size="sm" variant="pill" :color="getStatusProps(value).color" class="w-fit">
        {{ getStatusProps(value).label }}
      </UBadge>
    </template>

    <!-- actions -->
    <template #cell-actions="{ row }">
      <div class="flex items-center gap-4">
        {{ console.log('row:', row) }}
        <NuxtLink :to="`/backoffice/clients/${row.id}/edit`" class="text-blue-500 hover:text-blue-800">
          <EditIcon class="w-6 h-6" />
        </NuxtLink>
        <button class="text-red-500 hover:text-red-700">
          <TrashIcon class="w-6 h-6" />
        </button>
      </div>
    </template>

  </UTable>


</template>