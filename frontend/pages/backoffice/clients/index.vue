<script setup lang="ts">
  import UTable from '~/components/organisms/UTable.vue';
  import UBadge from '~/components/atoms/UBadge.vue';
  import TrashIcon from "~/components/atoms/icons/TrashIcon.vue";
  import EditIcon from "~/components/atoms/icons/EditIcon.vue";
  import ConfirmPopover from '~/components/ConfirmPopover.vue'
  import EyeView from '~/components/atoms/icons/EyeView.vue'
  import { useRuntimeConfig } from '#app'

  definePageMeta({
    layout: 'backoffice',
  })

  const { public: { apiUrl } } = useRuntimeConfig()

  const { data: clientData, pending, error } = await useFetch('/api/clients', {
    baseURL: apiUrl,
  })

  const clients = computed(() => clientData.value || [])

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

  const successMsg = ref('')
  const errorMsg = ref('')

  async function refreshClients() {
    const { data } = await useFetch('/api/clients', { baseURL: apiUrl })
    clientData.value = data.value
  }

  async function deleteClient(id: string) {

    successMsg.value = ''
    errorMsg.value = ''

    try {
      await $fetch(`/api/clients/${id}`, {
        method: 'DELETE',
        baseURL: apiUrl,
      })

      await refreshClients()

      successMsg.value = 'Client supprimé avec succès.'
    } catch (error: any) {
      errorMsg.value = error?.data?.message || 'Erreur lors de la suppression.'
      console.error(error)
    }
  }

</script>

<template>
  <div class="space-y-6">
    <p class="text-2xl font-semibold">Clients</p>

    <div v-if="successMsg" class="text-green-600 text-sm">{{ successMsg }}</div>
    <div v-if="errorMsg" class="text-red-600 text-sm">{{ errorMsg }}</div>

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
          <NuxtLink
              :to="`/backoffice/clients/${row.id}`"
              class="text-brand-600 hover:text-brand-700"
          >
            <EyeView class="w-6 h-6" />
          </NuxtLink>

          <NuxtLink
              :to="`/backoffice/clients/${row.id}/edit`"
              class="text-blue-500 hover:text-blue-800"
          >
            <EditIcon class="w-6 h-6" />
          </NuxtLink>

          <ConfirmPopover
              :itemName="row.client"
              @confirm="deleteClient(row.id)"
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
