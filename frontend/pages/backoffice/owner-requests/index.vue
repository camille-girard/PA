<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import UTable from '~/components/organisms/UTable.vue'
import UBadge from '~/components/atoms/UBadge.vue'
import UButton from '~/components/atoms/UButton.vue'
import CheckCircleIcon from "~/components/atoms/icons/CheckCircleIcon.vue"
import XIcon from "~/components/atoms/icons/XIcon.vue"
import { useRuntimeConfig } from '#app'
import { useAuthFetch } from '~/composables/useAuthFetch'
import { useToast } from '~/composables/useToast'

interface ApiError {
  status?: number
  data?: {
    message?: string
    error?: string
  }
  message?: string
}

definePageMeta({
  layout: 'backoffice',
  middleware: 'admin',
})

const toast = useToast()
const { public: { apiUrl } } = useRuntimeConfig()

interface OwnerRequest {
  id: number
  message: string
  createdAt: string
  reviewed: boolean
  user: {
    id: number
    firstName: string
    lastName: string
    email: string
  }
}

const ownerRequestData = ref<OwnerRequest[]>([])
const pending = ref(false)
const selectedRequest = ref<OwnerRequest | null>(null)
const showDetailModal = ref(false)
const actionPending = ref(false)

const columns = [
  { key: 'client', label: 'Client', sortable: true },
  { key: 'email', label: 'Email' },
  { key: 'message', label: 'Message' },
  { key: 'createdAt', label: 'Date de demande' },
  { key: 'status', label: 'Statut' },
  { key: 'actions', label: 'Actions' }
]

const ownerRequests = computed(() => ownerRequestData.value || [])

const ownerRequestsData = computed(() =>
    ownerRequests.value.map(request => ({
      id: request.id,
      client: `${request.user?.firstName} ${request.user?.lastName}`,
      email: request.user?.email,
      message: request.message.length > 50 ? request.message.substring(0, 50) + '...' : request.message,
      createdAt: new Date(request.createdAt).toLocaleDateString('fr-FR'),
      status: request.reviewed ? 'reviewed' : 'pending',
      _original: request,
    }))
)

function getStatusProps(status: string) {
  switch (status.toLowerCase()) {
    case 'pending':
      return { label: 'En cours', color: 'warning' }
    case 'reviewed':
      return { label: 'Traité', color: 'success' }
    default:
      return { label: status, color: 'brand' }
  }
}

function openDetailModal(request: OwnerRequest) {
  selectedRequest.value = request
  showDetailModal.value = true
}

function closeDetailModal() {
  showDetailModal.value = false
  selectedRequest.value = null
}

async function loadOwnerRequests() {
  pending.value = true
  try {
    const { data, error } = await useAuthFetch('/api/owner-requests', { baseURL: apiUrl })
    if (error.value) throw error.value
    ownerRequestData.value = data.value || []
  } catch (err) {
    toast.error('Erreur', extractErrorMessage(err))
  } finally {
    pending.value = false
  }
}

async function refreshOwnerRequests() {
  await loadOwnerRequests()
}

async function acceptRequest(id: string | number) {
  actionPending.value = true
  try {
    const { error } = await useAuthFetch(`/api/owner-requests/${id}/accept`, {
      method: 'PATCH',
      baseURL: apiUrl,
    })

    if (error.value) throw error.value

    await refreshOwnerRequests()
    toast.success('Succès', 'Demande acceptée avec succès. L\'utilisateur est maintenant propriétaire.')
    closeDetailModal()

    setTimeout(() => window.location.reload(), 1500)
  } catch (err) {
    toast.error('Erreur', extractErrorMessage(err))
    console.error(err)
  } finally {
    actionPending.value = false
  }
}

async function rejectRequest(id: string | number) {
  actionPending.value = true
  try {
    const { error } = await useAuthFetch(`/api/owner-requests/${id}/reject`, {
      method: 'PATCH',
      baseURL: apiUrl,
    })

    if (error.value) throw error.value

    await refreshOwnerRequests()
    toast.success('Succès', 'Demande rejetée avec succès.')
    closeDetailModal()
  } catch (err) {
    toast.error('Erreur', extractErrorMessage(err))
    console.error(err)
  } finally {
    actionPending.value = false
  }
}

onMounted(() => {
  loadOwnerRequests()
})

function extractErrorMessage(err: unknown): string {
  if (typeof err === 'object' && err !== null) {
    const e = err as ApiError

    if (e.data?.error) {
      return e.data.error
    }

    if (e.data?.message) {
      return e.data.message
    }

    if (e.message) {
      return e.message
    }
  }

  return 'Erreur inattendue'
}
</script>

<template>
  <div class="space-y-6">
    <p class="text-2xl font-semibold">Demandes de propriétaires</p>

    <div v-if="pending" class="text-gray-600">Chargement…</div>
    <div v-else>

      <UTable :columns="columns" :data="ownerRequestsData">
        <template #cell-status="{ value }">
          <UBadge size="sm" variant="pill" :color="getStatusProps(value).color" class="w-fit">
            {{ getStatusProps(value).label }}
          </UBadge>
        </template>

        <template #cell-actions="{ row }">
          <div class="flex items-center gap-2">
            <UButton
                size="sm"
                variant="outline"
                class="flex items-center gap-1"
                @click="openDetailModal(row._original)"
            >
              Voir détail
            </UButton>

            <div v-if="row.status === 'pending'" class="flex items-center gap-2">
              <UButton
                  size="sm"
                  variant="primary"
                  :disabled="pending"
                  class="flex items-center gap-1"
                  @click="acceptRequest(row.id)"
              >
                <CheckCircleIcon class="w-4 h-4" />
                Accepter
              </UButton>

              <UButton
                  size="sm"
                  variant="secondary"
                  :disabled="pending"
                  class="flex items-center gap-1"
                  @click="rejectRequest(row.id)"
              >
                <XIcon class="w-4 h-4" />
                Refuser
              </UButton>
            </div>
            <div v-else class="text-gray-500 text-sm">
              Traité
            </div>
          </div>
        </template>
      </UTable>
    </div>

    <div v-if="showDetailModal" style="margin: 0;" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-lg max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6">
          <!-- Header -->
          <div class="flex justify-between items-start mb-6">
            <h2 class="text-xl font-semibold">Détail de la demande</h2>
            <button
                class="text-gray-400 hover:text-gray-600 text-2xl"
                @click="closeDetailModal"
            >
              ×
            </button>
          </div>

          <div v-if="selectedRequest" class="space-y-4">
            <div class="bg-gray-50 p-4 rounded-lg">
              <h3 class="font-medium mb-3 text-gray-800">Informations du client</h3>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                <div>
                  <span class="font-medium text-gray-600">Nom :</span>
                  <span class="ml-2">{{ selectedRequest.user.firstName }} {{ selectedRequest.user.lastName }}</span>
                </div>
                <div>
                  <span class="font-medium text-gray-600">Email :</span>
                  <span class="ml-2">{{ selectedRequest.user.email }}</span>
                </div>
                <div>
                  <span class="font-medium text-gray-600">Date de demande :</span>
                  <span class="ml-2">{{ new Date(selectedRequest.createdAt).toLocaleDateString('fr-FR', {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                  }) }}</span>
                </div>
                <div>
                  <span class="font-medium text-gray-600">Statut :</span>
                  <UBadge
                      size="sm"
                      variant="pill"
                      :color="getStatusProps(selectedRequest.reviewed ? 'reviewed' : 'pending').color"
                      class="ml-2"
                  >
                    {{ getStatusProps(selectedRequest.reviewed ? 'reviewed' : 'pending').label }}
                  </UBadge>
                </div>
              </div>
            </div>

            <div class="bg-gray-50 p-4 rounded-lg">
              <h3 class="font-medium mb-3 text-gray-800">Message de motivation</h3>
              <div class="text-sm text-gray-700 whitespace-pre-wrap">{{ selectedRequest.message }}</div>
            </div>

            <div v-if="!selectedRequest.reviewed" class="flex justify-end gap-3 pt-4 border-t">
              <UButton
                  variant="secondary"
                  :disabled="actionPending"
                  class="flex items-center gap-2"
                  @click="rejectRequest(selectedRequest.id)"
              >
                <XIcon class="w-4 h-4" />
                {{ actionPending ? 'Traitement...' : 'Refuser la demande' }}
              </UButton>

              <UButton
                  variant="primary"
                  :disabled="actionPending"
                  class="flex items-center gap-2"
                  @click="acceptRequest(selectedRequest.id)"
              >
                <CheckCircleIcon class="w-4 h-4" />
                {{ actionPending ? 'Traitement...' : 'Accepter et promouvoir en propriétaire' }}
              </UButton>
            </div>

            <div v-else class="flex justify-center pt-4 border-t">
              <div class="text-gray-500 text-sm flex items-center gap-2">
                <CheckCircleIcon class="w-4 h-4" />
                Cette demande a déjà été traitée
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>