<script setup lang="ts">
import { ref, computed, watch } from 'vue'
import { useRoute } from 'vue-router'
import UBadge from '~/components/atoms/UBadge.vue'
import UCard from '~/components/molecules/UCard.vue'
import UTable from '~/components/organisms/UTable.vue'
import { useRuntimeConfig } from '#app'
import { useAuthFetch } from '~/composables/useAuthFetch'

definePageMeta({
  layout: 'backoffice',
  middleware: 'admin',
})

const route = useRoute()
const id = ref<string | undefined>(undefined)
const { public: { apiUrl } } = useRuntimeConfig()

interface Booking {
  id: number
  accommodation?: {
    name?: string
  }
  startDate: string
  endDate: string
  status: string
  totalPrice: number
}

interface Client {
  id: number
  firstName?: string
  lastName?: string
  email?: string
  phone?: string
  createdAt: string
  verified: boolean
  preferences?: string[]
  bookings?: Booking[]
}

const client = ref<Client | null>(null)
const pending = ref(false)
const errorMsg = ref('')

async function loadClient(clientId: string) {
  pending.value = true
  errorMsg.value = ''
  try {
    const { data } = await useAuthFetch<{ client: Client }>(`/api/clients/${clientId}`, {
      baseURL: apiUrl,
    })
    client.value = data.value?.client ?? null
  } catch (error: any) {
    errorMsg.value = error?.data?.message || 'Erreur lors du chargement du client.'
    console.error(error)
  } finally {
    pending.value = false
  }
}

watch(
    () => route.params.id,
    (newId) => {
      if (typeof newId === 'string' && newId !== '') {
        id.value = newId
        loadClient(newId)
      }
    },
    { immediate: true }
)

const getStatusProps = (verified: boolean | undefined) =>
    verified
        ? { label: 'Vérifié', color: 'success' }
        : { label: 'Non vérifié', color: 'error' }

const bookings = computed(() =>
    client.value?.bookings?.map((b: Booking) => ({
      id: b.id,
      accommodation: b.accommodation?.name ?? '—',
      startDate: new Date(b.startDate).toLocaleDateString(),
      endDate: new Date(b.endDate).toLocaleDateString(),
      status: b.status,
      totalPrice: `${b.totalPrice?.toFixed(2)} €`,
    })) ?? []
)
</script>

<template>
  <div class="space-y-6">
    <h1 class="text-2xl font-semibold">Fiche client</h1>

    <div v-if="pending" class="text-gray-600">Chargement…</div>
    <div v-else-if="errorMsg" class="text-red-600">{{ errorMsg }}</div>
    <div v-else-if="!client" class="text-gray-600">Aucun client trouvé.</div>
    <div v-else class="space-y-6">
      <UCard>
        <template #header>
          <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center">
            <div class="text-lg font-medium">
              {{ client.firstName }} {{ client.lastName }}
            </div>
            <UBadge
                :color="getStatusProps(client.verified).color"
                variant="pill"
            >
              {{ getStatusProps(client.verified).label }}
            </UBadge>
          </div>
        </template>

        <div class="grid md:grid-cols-2 gap-6">
          <div><strong>Email :</strong> {{ client.email }}</div>
          <div><strong>Téléphone :</strong> {{ client.phone || 'Non renseigné' }}</div>
          <div><strong>Date de création :</strong> {{ new Date(client.createdAt).toLocaleDateString() }}</div>
          <div><strong>Préférences :</strong> {{ client.preferences?.join(', ') || '—' }}</div>
        </div>
      </UCard>

      <div>
        <h2 class="text-lg font-semibold mb-4">Réservations</h2>
        <UTable
            :columns="[
            { key: 'accommodation', label: 'Hébergement' },
            { key: 'startDate', label: 'Début' },
            { key: 'endDate', label: 'Fin' },
            { key: 'status', label: 'Statut' },
            { key: 'totalPrice', label: 'Prix total' },
          ]"
            :data="bookings"
            :loading="pending"
        />
      </div>
    </div>
  </div>
</template>
