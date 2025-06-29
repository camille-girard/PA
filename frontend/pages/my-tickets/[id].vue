<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAuthFetch } from '~/composables/useAuthFetch'
import UHeader from '~/components/UHeader.vue'
import UFooter from '~/components/UFooter.vue'
import UButton from '~/components/atoms/UButton.vue'
import UTextarea from '~/components/atoms/UTextarea.vue'
import UBadge from '~/components/atoms/UBadge.vue'
import UCard from '~/components/molecules/UCard.vue'
import { useRuntimeConfig } from '#app'

definePageMeta({
  middleware: 'owner'
})

const { public: { apiUrl } } = useRuntimeConfig()
const route = useRoute()
const router = useRouter()

const ticket = ref<any>(null)
const isLoading = ref(true)
const error = ref<string | null>(null)
const newMessage = ref('')
const savingMessage = ref(false)
const successMsg = ref('')
const errorMsg = ref('')

async function fetchTicket() {
  isLoading.value = true
  error.value = null
  try {
    const { data } = await useAuthFetch(`/api/tickets/${route.params.id}`, {
      baseURL: apiUrl,
    })
    ticket.value = data.value
  } catch (e: any) {
    error.value = e?.data?.message || 'Erreur de chargement'
  } finally {
    isLoading.value = false
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
  <main class="w-full h-full">
    <UHeader />

    <div class="max-w-4xl mx-auto px-4 py-16">
      <div v-if="isLoading" class="text-center text-gray-500">Chargement...</div>
      <div v-else-if="error" class="text-center text-red-600">{{ error }}</div>
      <div v-else-if="ticket">
        <div class="flex flex-col gap-6 items-center mb-8">
          <div>
            <h1 class="text-3xl text-center font-bold pt-20 mb-2">Détails du ticket</h1>
            <p class="text-gray-600">Consultez les messages et répondez si nécessaire</p>
          </div>
          <UButton variant="primary" @click="router.push('/my-tickets')">
            ← Retour à la liste des tickets
          </UButton>
        </div>

        <UCard class="mb-8">
          <template #header>
            <div class="flex justify-between items-center">
              <div class="font-semibold text-lg">{{ ticket.title }}</div>
              <UBadge :color="statusColor(ticket.status)">
                {{ statusLabel(ticket.status) }}
              </UBadge>
            </div>
          </template>
          <div class="text-sm text-gray-700 space-y-2">
            <p><strong>Créé le :</strong> {{ new Date(ticket.createdAt).toLocaleString() }}</p>
            <p><strong>Dernière mise à jour :</strong> {{ new Date(ticket.updatedAt).toLocaleString() }}</p>
            <p v-if="ticket.description"><strong>Description :</strong> {{ ticket.description }}</p>
          </div>
        </UCard>

        <div class="mt-8">
          <h2 class="text-xl font-semibold mb-4">Messages</h2>
          <div v-if="ticket.ticketMessages.length === 0" class="text-gray-500 italic">
            Aucun message pour ce ticket.
          </div>
          <div v-else class="space-y-4">
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
        </div>

        <div v-if="ticket.status !== 'CLOSED'" class="mt-10 space-y-2">
          <label class="block text-sm font-medium">Ajouter une réponse</label>
          <UTextarea v-model="newMessage" placeholder="Écrire un message..." rows="4" />
          <UButton :disabled="savingMessage || !newMessage" @click="sendMessage">
            {{ savingMessage ? 'Envoi...' : 'Envoyer' }}
          </UButton>
          <div v-if="successMsg" class="text-green-600 text-sm">{{ successMsg }}</div>
          <div v-if="errorMsg" class="text-red-600 text-sm">{{ errorMsg }}</div>
        </div>

        <div v-else class="mt-10 text-center text-gray-500 italic">
          Ce ticket est fermé. Vous ne pouvez plus répondre.
        </div>
      </div>
    </div>

    <UFooter />
  </main>
</template>
