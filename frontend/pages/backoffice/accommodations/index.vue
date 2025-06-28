<script setup lang="ts">
import { ref, computed } from 'vue'
import { useRuntimeConfig } from '#app'
import { useAuthFetch } from '~/composables/useAuthFetch'

import UTable from '~/components/organisms/UTable.vue'
import TrashIcon from '~/components/atoms/icons/TrashIcon.vue'
import EditIcon from '~/components/atoms/icons/EditIcon.vue'
import EyeView from '~/components/atoms/icons/EyeView.vue'
import ConfirmPopover from '~/components/ConfirmPopover.vue'
import UBadge from '~/components/atoms/UBadge.vue'

definePageMeta({
  layout: 'backoffice',
})

const { public: { apiUrl } } = useRuntimeConfig()

const { data: accommodationsData } = await useAuthFetch('/api/accommodations', { baseURL: apiUrl })

const accommodations = computed(() => accommodationsData.value || [])

const columns = [
  { key: 'name', label: 'Nom', sortable: true },
  { key: 'owner', label: 'Hôte' },
  { key: 'address', label: 'Adresse' },
  { key: 'price', label: 'Prix (€) / nuit' },
  { key: 'capacity', label: 'Capacité' },
  { key: 'theme', label: 'Thème' },
  { key: 'availability', label: 'Disponibilité' },
  { key: 'actions', label: '' },
]

interface Booking {
  startDate: string
  endDate: string
}

interface AccommodationRow {
  id: number
  name: string
  owner?: { firstName: string; lastName: string }
  city?: string
  country?: string
  address?: string
  price: number
  capacity: number
  theme?: { name: string }
  bookings?: Booking[]
}

const today = new Date().toISOString().slice(0, 10)

function isAvailableNow(acc: AccommodationRow) {
  return !(acc.bookings?.some(b => b.startDate <= today && b.endDate >= today))
}

const accommodationsTableData = computed(() =>
    accommodations.value.map(acc => ({
      id: acc.id,
      name: acc.name,
      owner: acc.owner ? `${acc.owner.firstName} ${acc.owner.lastName}` : 'N/A',
      address: [acc.city, acc.country].filter(Boolean).join(', '),
      availability: isAvailableNow(acc) ? 'Disponible' : 'Indisponible',
      price: acc.price.toFixed(2),
      capacity: acc.capacity,
      theme: acc.theme?.name || 'Aucun',
      _original: acc,
    }))
)

const successMsg = ref('')
const errorMsg = ref('')

async function refreshAccommodations() {
  const { data } = await useAuthFetch('/api/accommodations', { baseURL: apiUrl })
  accommodationsData.value = data.value
}

async function deleteAccommodation(id: number) {
  successMsg.value = ''
  errorMsg.value = ''
  try {
    await $fetch(`/api/accommodations/${id}`, {
      method: 'DELETE',
      baseURL: apiUrl,
    })
    await refreshAccommodations()
    successMsg.value = 'Hébergement supprimé avec succès.'
  } catch (err: unknown) {
    if (typeof err === 'object' && err && 'data' in err) {
      errorMsg.value = (err as { data?: { message?: string } }).data?.message || 'Erreur lors de la suppression.'
    } else {
      errorMsg.value = 'Erreur lors de la suppression.'
    }
    console.error(err)
  }
}
</script>

<template>
  <div class="space-y-6">
    <p class="text-2xl font-semibold">Hébergements</p>

    <div v-if="successMsg" class="text-green-600 text-sm">{{ successMsg }}</div>
    <div v-if="errorMsg" class="text-red-600 text-sm">{{ errorMsg }}</div>

    <UTable :columns="columns" :data="accommodationsTableData">
      <template #cell-availability="{ row }">
        <UBadge
            :color="row.availability === 'Disponible' ? 'success' : 'error'"
            variant="pill"
            size="sm"
        >
          {{ row.availability }}
        </UBadge>
      </template>

      <template #cell-actions="{ row }">
        <div class="flex items-center gap-4">
          <NuxtLink
              :to="`/backoffice/accommodations/${row.id}`"
              class="text-brand-600 hover:text-brand-700"
          >
            <EyeView class="w-6 h-6" />
          </NuxtLink>

          <NuxtLink
              :to="`/backoffice/accommodations/${row.id}/edit`"
              class="text-blue-500 hover:text-blue-800"
          >
            <EditIcon class="w-6 h-6" />
          </NuxtLink>

          <ConfirmPopover
              :item-name="row.name"
              @confirm="deleteAccommodation(row.id)"
          >
            <template #trigger>
              <button class="text-red-500 hover:text-red-700">
                <TrashIcon class="w-6 h-6" />
              </button>
            </template>
          </ConfirmPopover>
        </div>
      </template>
    </UTable>
  </div>
</template>
