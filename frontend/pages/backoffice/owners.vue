<script setup lang="ts">

import UTable from '~/components/organisms/UTable.vue';
import UBadge from '~/components/atoms/UBadge.vue';
import FullStarIcon from "~/components/atoms/icons/FullStarIcon.vue";
import HalfStarIcon from "~/components/atoms/icons/HalfStarIcon.vue";
import TrashIcon from "~/components/atoms/icons/TrashIcon.vue";
import EditIcon from "~/components/atoms/icons/EditIcon.vue";

const { data: ownerData, pending, error } = await useFetch('/api/owners', {
  baseURL: 'http://localhost',
})

const owners = computed(() => ownerData.value || [])

definePageMeta({
  layout: 'backoffice',
})

const columns = [
  { key: 'owner', label: 'Hôte', sortable: true },
  { key: 'phone', label: 'Téléphone' },
  { key: 'email', label: 'Email' },
  { key: 'accommodationCount', label: 'Logements' },
  { key: 'notation', label: 'Note' },
  { key: 'status', label: 'Status' },
  { key: 'actions', label: '' }
]

const ownersData = computed(() =>
    owners.value.map(owner => ({
      owner: `${owner.firstName} ${owner.lastName}`,
      phone: owner.phone || 'Non renseigné',
      email: owner.email,
      accommodationCount: owner.accommodationCount ?? 0,
      notation: owner.notation ?? 0,
      status: owner.isVerified ? 'active' : 'inactive',
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

  <p class="text-2xl font-semibold">Hôtes</p>

  <UTable :columns="columns" :data="ownersData">
    <!-- status -->
    <template #cell-status="{ value }">
      <UBadge size="sm" variant="pill" :color="getStatusProps(value).color" class="w-fit">
        {{ getStatusProps(value).label }}
      </UBadge>
    </template>

    <template #cell-notation="{ value }">
      <div class="flex items-center gap-0.5">
        <template v-for="i in 5" :key="i">
          <component
              :is="value >= i ? FullStarIcon : value >= i - 0.5 ? HalfStarIcon : EmptyStarIcon"
          />
        </template>
      </div>
    </template>



    <!-- actions -->
    <template #cell-actions>
      <div class="flex items-center gap-4">
        <button class="text-blue-500 hover:text-blue-800">
          <EditIcon class="w-6 h-6" />
        </button>
        <button class="text-red-500 hover:text-red-700">
          <TrashIcon class="w-6 h-6" />
        </button>
      </div>
    </template>
  </UTable>


</template>


