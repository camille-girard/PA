<script setup lang="ts">

import UTable from '~/components/organisms/UTable.vue';
import UBadge from '~/components/atoms/UBadge.vue';
import PinIcon from "~/components/atoms/icons/PinIcon.vue";
import InfoCircleIcon from "~/components/atoms/icons/InfoCircleIcon.vue";

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
  { key: 'status', label: 'Status' },
  { key: 'actions', label: '' }
]

const ownersData = computed(() =>
    owners.value.map(owner => ({
      owner: `${owner.firstName} ${owner.lastName}`,
      phone: owner.phone || 'Non renseigné',
      email: owner.email,
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


