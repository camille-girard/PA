<script setup lang="ts">
  import { ref, onMounted } from 'vue'
  import { useRoute } from 'vue-router'
  import { useAuthFetch } from '~/composables/useAuthFetch'
  import UCard from '~/components/molecules/UCard.vue'
  import UButton from '~/components/atoms/UButton.vue'
  import Textarea from '~/components/atoms/UTextarea.vue'
  import UBadge from '~/components/atoms/UBadge.vue'
  import { useRuntimeConfig } from '#app'

  definePageMeta({
    layout: 'backoffice',
    middleware: 'admin',
  })

  const { public: { apiUrl } } = useRuntimeConfig()
  const route = useRoute()

  const ticket = ref(null)
  const loading = ref(true)
  const error = ref<string | null>(null)
  const newMessage = ref('')
  const savingMessage = ref(false)
  const updatingStatus = ref(false)
  const successMsg = ref('')
  const errorMsg = ref('')

  async function fetchTicket() {
    loading.value = true
    error.value = null
    try {
      const { data } = await useAuthFetch(`/api/tickets/${route.params.id}`, {
        baseURL: apiUrl,
      })
      ticket.value = data.value
    } catch (e: any) {
      error.value = e?.data?.message || 'Erreur de chargement'
    } finally {
      loading.value = false
    }
  }

  onMounted(fetchTicket)

  async function sendMessage() {
    if (!newMessage.value) return
    savingMessage.value = true
    errorMsg.value = ''
    successMsg.value = ''
    try {
      await useAuthFetch(`/api/tickets/${ticket.value.id}/messages`, {
        method: 'POST',
        baseURL: apiUrl,
        body: { content: newMessage.value },
      })
      newMessage.value = ''
      successMsg.value = 'Message envoyé'
      await fetchTicket()
    } catch (e: any) {
      errorMsg.value = e?.data?.message || 'Erreur lors de l’envoi.'
    } finally {
      savingMessage.value = false
    }
  }

  async function changeStatus(newStatus: string) {
    if (ticket.value.status === newStatus) return
    updatingStatus.value = true
    try {
      await useAuthFetch(`/api/admin/tickets/${ticket.value.id}/status`, {
        method: 'PATCH',
        baseURL: apiUrl,
        body: { status: newStatus },
      })
      await fetchTicket()
    } catch (e) {
      console.error(e)
    } finally {
      updatingStatus.value = false
    }
  }

  function statusLabel(status: string) {
    switch (status) {
      case 'OPEN': return 'Ouvert'
      case 'IN_PROGRESS': return 'En cours'
      case 'CLOSED': return 'Fermé'
      default: return status
    }
  }

  function statusColor(status: string) {
    switch (status) {
      case 'OPEN': return 'error'
      case 'IN_PROGRESS': return 'warning'
      case 'CLOSED': return 'success'
      default: return 'gray'
    }
  }
</script>

<template>
  <div class="space-y-8">
    <h1 class="text-2xl font-semibold">Détails du ticket</h1>

    <div v-if="loading" class="text-gray-500">Chargement...</div>
    <div v-else-if="error" class="text-red-600">{{ error }}</div>

    <div v-else-if="ticket">
      <UCard>
        <template #header>
          <div class="flex justify-between items-center">
            <div class="font-semibold text-lg">{{ ticket.title }}</div>
            <UBadge :color="statusColor(ticket.status)">
              {{ statusLabel(ticket.status) }}
            </UBadge>
          </div>
        </template>
        <div class="text-sm text-gray-700 space-y-2">
          <p><strong>Propriétaire :</strong> {{ ticket.owner?.firstName }} {{ ticket.owner?.lastName }}</p>
          <p><strong>Créé le :</strong> {{ new Date(ticket.createdAt).toLocaleString() }}</p>
          <p><strong>Dernière mise à jour :</strong> {{ new Date(ticket.updatedAt).toLocaleString() }}</p>

          <div v-if="ticket.description" class="mt-2 p-3 bg-gray-50 rounded border text-gray-800">
            <p class="font-medium">Description :</p>
            <p class="whitespace-pre-wrap">{{ ticket.description }}</p>
          </div>
        </div>
      </UCard>

      <div class="mt-4 space-y-2">
        <label class="text-sm font-medium">Changer le statut :</label>
        <div class="flex gap-2 flex-wrap">
          <UButton
              v-for="status in ['OPEN', 'IN_PROGRESS', 'CLOSED']"
              :key="status"
              :variant="ticket.status === status ? 'primary' : 'outline'"
              :disabled="ticket.status === status || updatingStatus"
              @click="changeStatus(status)"
          >
            Passer à {{ statusLabel(status) }}
          </UButton>
        </div>
      </div>

      <div class="mt-8 space-y-4">
        <h2 class="text-xl font-semibold">Messages</h2>
        <div v-if="ticket.ticketMessages.length === 0" class="text-gray-500 italic">
          Aucun message pour ce ticket.
        </div>
        <div
            v-for="msg in ticket.ticketMessages"
            :key="msg.id"
            class="border rounded-lg p-4 bg-white shadow-sm"
        >
          <div class="flex justify-between items-center mb-2">
            <span class="font-medium">{{ msg.author?.firstName }} {{ msg.author?.lastName }}</span>
            <span class="text-xs text-gray-500">{{ new Date(msg.createdAt).toLocaleString() }}</span>
          </div>
          <p class="text-sm whitespace-pre-wrap">{{ msg.content }}</p>
        </div>
      </div>

      <div v-if="ticket.status !== 'CLOSED'" class="mt-8 space-y-2">
        <label class="block text-sm font-medium">Ajouter une réponse</label>
        <Textarea v-model="newMessage" placeholder="Écrire un message..." rows="3" />
        <UButton :disabled="savingMessage || !newMessage" @click="sendMessage">
          {{ savingMessage ? 'Envoi...' : 'Envoyer' }}
        </UButton>
        <div v-if="successMsg" class="text-green-600 text-sm">{{ successMsg }}</div>
        <div v-if="errorMsg" class="text-red-600 text-sm">{{ errorMsg }}</div>
      </div>
      <div v-else class="mt-8 text-sm text-gray-500 italic">
        Ce ticket est fermé. Il n'est plus possible d'ajouter de réponse.
      </div>
    </div>
  </div>
</template>
