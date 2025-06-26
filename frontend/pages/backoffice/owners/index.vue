<script setup lang="ts">
  import UTable from '~/components/organisms/UTable.vue'
  import UBadge from '~/components/atoms/UBadge.vue'
  import FullStarIcon from '~/components/atoms/icons/FullStarIcon.vue'
  import HalfStarIcon from '~/components/atoms/icons/HalfStarIcon.vue'
  import EmptyStarIcon from '~/components/atoms/icons/EmptyStarIcon.vue'
  import TrashIcon from '~/components/atoms/icons/TrashIcon.vue'
  import EditIcon from '~/components/atoms/icons/EditIcon.vue'
  import ConfirmPopover from '~/components/ConfirmPopover.vue'
  import EyeView from '~/components/atoms/icons/EyeView.vue'
  import { useRouter } from 'vue-router'
  import { useRuntimeConfig } from '#app'

  definePageMeta({ layout: 'backoffice' })

  const router = useRouter()
  const { public: { apiUrl } } = useRuntimeConfig()

  const { data: ownerData } = await useFetch('/api/owners', { baseURL: apiUrl })
  const owners = computed(() => ownerData.value || [])

  const successMsg = ref('')
  const errorMsg = ref('')

  async function refreshOwners() {
    const { data } = await useFetch('/api/owners', { baseURL: apiUrl })
    ownerData.value = data.value
  }

  async function deleteOwner(id: string) {
    successMsg.value = ''
    errorMsg.value = ''
    try {
      await $fetch(`/api/owners/${id}`, {
        method: 'DELETE',
        baseURL: apiUrl,
      })
      await refreshOwners()
      successMsg.value = 'Hôte supprimé avec succès.'
    } catch (error: any) {
      errorMsg.value = error?.data?.message || 'Erreur lors de la suppression.'
      console.error(error)
    }
  }

  const columns = [
    { key: 'owner', label: 'Hôte', sortable: true },
    { key: 'phone', label: 'Téléphone' },
    { key: 'email', label: 'Email' },
    { key: 'accommodationCount', label: 'Logements' },
    { key: 'notation', label: 'Note' },
    { key: 'status', label: 'Status' },
    { key: 'actions', label: '' },
  ]

  const ownersData = computed(() =>
      owners.value.map(owner => ({
        id: owner.id,
        owner: `${owner.firstName} ${owner.lastName}`,
        phone: owner.phone || 'Non renseigné',
        email: owner.email,
        accommodationCount: owner.accommodationCount ?? 0,
        notation: owner.notation ?? 0,
        status: owner.isVerified ? 'active' : 'inactive',
        _original: owner,
      }))
  )

  function getStatusProps(status: string) {
    switch (status.toLowerCase()) {
      case 'active':
        return { label: 'Actif', color: 'success' }
      case 'inactive':
        return { label: 'Inactif', color: 'error' }
      case 'pending':
      case 'en attente':
        return { label: 'En attente', color: 'warning' }
      default:
        return { label: status, color: 'brand' }
    }
  }
</script>


<template>
  <div class="space-y-6">
    <p class="text-2xl font-semibold">Hôtes</p>

    <div v-if="successMsg" class="text-green-600 text-sm">{{ successMsg }}</div>
    <div v-if="errorMsg" class="text-red-600 text-sm">{{ errorMsg }}</div>

    <UTable :columns="columns" :data="ownersData">
      <template #cell-status="{ value }">
        <UBadge size="sm" variant="pill" :color="getStatusProps(value).color" class="w-fit">
          {{ getStatusProps(value).label }}
        </UBadge>
      </template>

      <template #cell-notation="{ value }">
        <div class="flex items-center gap-0.5">
          <component
              v-for="i in 5"
              :key="i"
              :is="value >= i ? FullStarIcon : value >= i - 0.5 ? HalfStarIcon : EmptyStarIcon"
          />
        </div>
      </template>

      <template #cell-actions="{ row }">
        <div class="flex items-center gap-4">

          <NuxtLink
              :to="`/backoffice/owners/${row.id}`"
              class="text-brand-600 hover:text-brand-700"
          >
            <EyeView class="w-6 h-6" />
          </NuxtLink>


          <NuxtLink
              :to="`/backoffice/owners/${row.id}/edit`"
              class="text-blue-500 hover:text-blue-800"
          >
            <EditIcon class="w-6 h-6" />
          </NuxtLink>

          <ConfirmPopover
              :itemName="row.owner"
              @confirm="deleteOwner(row.id)"
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



