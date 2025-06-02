<script setup lang="ts">
import UTable from '~/components/organisms/UTable.vue'
import UBadge from '~/components/atoms/UBadge.vue'
import EditIcon from '~/components/atoms/icons/EditIcon.vue'
import TrashIcon from '~/components/atoms/icons/TrashIcon.vue'

const { data: bookingData, pending, error } = await useFetch('/api/bookings', {
  baseURL: 'http://localhost',
})

const bookings = computed(() => bookingData.value || [])

definePageMeta({
  layout: 'backoffice',
})

const columns = [
  { key: 'client', label: 'Client', sortable: true },
  { key: 'owner', label: 'Hôte' },
  { key: 'accommodation', label: 'Hébergement' },
  { key: 'address', label: 'Adresse' },
  { key: 'startDate', label: 'Début' },
  { key: 'endDate', label: 'Fin' },
  { key: 'totalPrice', label: 'Prix' },
  { key: 'status', label: 'Statut' },
  { key: 'actions', label: '' },
]

const bookingsData = computed(() =>
    bookings.value.map(b => ({
      client: `${b.client?.firstName || ''} ${b.client?.lastName || ''}`,
      owner: `${b.accommodation?.owner?.firstName || ''} ${b.accommodation?.owner?.lastName || ''}`,
      accommodation: b.accommodation?.name,
      address: b.accommodation?.address,
      startDate: new Date(b.startDate).toLocaleDateString(),
      endDate: new Date(b.endDate).toLocaleDateString(),
      totalPrice: `${b.totalPrice.toFixed(2)} €`,
      status: b.status.toLowerCase(),
    }))
)

function getStatusProps(status: string) {
  switch (status) {
    case 'accepted':
      return { label: 'Acceptée', color: 'success' }
    case 'pending':
      return { label: 'En attente', color: 'warning' }
    case 'refused':
      return { label: 'Refusée', color: 'error' }
    default:
      return { label: status, color: 'gray' }
  }
}
</script>

<template>
  <div>
    <p class="text-2xl font-semibold mb-4">Réservations</p>

    <!-- Wrapping the table in a horizontal scroll container -->
    <div class="w-full overflow-x-auto">
      <div class="min-w-[800px]">
        <UTable :columns="columns" :data="bookingsData">
          <template #cell-status="{ value }">
            <UBadge size="sm" variant="pill" :color="getStatusProps(value).color" class="w-max">
              {{ getStatusProps(value).label }}
            </UBadge>
          </template>

          <template #cell-actions>
            <div class="flex items-center gap-4">
              <button class="text-blue-500 hover:text-bblue-800">
                <EditIcon class="w-6 h-6" />
              </button>
              <button class="text-red-500 hover:text-red-700">
                <TrashIcon class="w-6 h-6" />
              </button>
            </div>
          </template>
        </UTable>
      </div>
    </div>
  </div>
</template>


