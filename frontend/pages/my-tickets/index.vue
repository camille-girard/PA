<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRuntimeConfig } from '#app'
import UHeader from '~/components/UHeader.vue'
import UFooter from '~/components/UFooter.vue'
import UButton from '~/components/atoms/UButton.vue'
import UCard from '~/components/molecules/UCard.vue'
import UBadge from '~/components/atoms/UBadge.vue'

const tickets = ref([])
const isLoading = ref(true)
const error = ref<string | null>(null)

const config = useRuntimeConfig()

definePageMeta({
  middleware: 'owner'
})

onMounted(async () => {
  try {
    const res = await fetch(`${config.public.apiUrl}/api/tickets`, {
      credentials: 'include',
    })

    if (!res.ok) {
      throw new Error(`Erreur serveur: ${res.statusText}`)
    }

    const json = await res.json()
    tickets.value = json
  } catch (err: any) {
    error.value = err.message
  } finally {
    isLoading.value = false
  }
})

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

    <div class="max-w-7xl w-full mx-auto pt-8 px-4">
      <section class="w-full pt-8">
        <div class="py-20 rounded-2xl flex items-center justify-center relative">
          <div class="text-center z-10">
            <h1 class="text-h1">Mes tickets</h1>
            <p class="mt-4">Suivez vos demandes de support et leur statut</p>
          </div>
        </div>

        <div v-if="isLoading" class="text-center text-gray-500">Chargement...</div>
        <div v-else-if="error" class="text-center text-red-500">{{ error }}</div>
        <div v-else-if="tickets.length === 0" class="text-center text-gray-400 text-lg">
          Vous n’avez encore créé aucun ticket.
        </div>
        <div v-else class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
          <NuxtLink
              v-for="ticket in tickets"
              :key="ticket.id"
              :to="`/my-tickets/${ticket.id}`"
              class="block"
          >
            <UCard class="cursor-pointer hover:shadow-md transition">
              <template #header>
                <div class="flex justify-between items-center">
                  <div class="font-semibold">{{ ticket.title }}</div>
                  <UBadge :color="statusColor(ticket.status)">
                    {{ statusLabel(ticket.status) }}
                  </UBadge>
                </div>
              </template>
              <div class="text-sm text-gray-600 space-y-1">
                <p><strong>Créé le :</strong> {{ new Date(ticket.createdAt).toLocaleDateString() }}</p>
                <p v-if="ticket.description" class="truncate">
                  {{ ticket.description }}
                </p>
              </div>
            </UCard>
          </NuxtLink>
        </div>

        <div class="mt-10 text-center">
          <NuxtLink to="/my-tickets/create">
            <UButton class="mx-auto">Créer un ticket</UButton>
          </NuxtLink>
        </div>
      </section>
    </div>

    <UFooter />
  </main>
</template>
