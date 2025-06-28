<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '~/stores/auth'
import BookingItem from '~/components/BookingItem.vue'

const router = useRouter()
const auth = useAuthStore()
if (!auth.isAuthenticated) router.push('/login')

const { $api } = useNuxtApp()

const {
  data: bookings,
  pending,
  error,
  refresh: refreshBookings,
} = await useAsyncData('myBookings', async () => {
  const data = await $fetch($api('/api/bookings/me'), { credentials: 'include' })
  return data.map((b: any) => ({
    ...b,
    startDate: new Date(b.startDate),
    endDate: new Date(b.endDate),
  }))
})

const showCancelModal = ref(false)
const selectedBookingId = ref<number | null>(null)

async function cancelBooking() {
  if (!selectedBookingId.value) return
  try {
    await $fetch($api(`/api/bookings/${selectedBookingId.value}`), {
      method: 'DELETE',
      credentials: 'include',
    })
    await refreshBookings()
    showCancelModal.value = false
  } catch (e) {
    console.error(e)
    alert("Erreur lors de l'annulation.")
  }
}

function contactHost(bookingId: number) {
  alert(`Fonction à implémenter pour contacter l'hôte de la réservation #${bookingId}`)
}
</script>

<template>
  <main class="w-full h-full">
    <UHeader />
    <div class="max-w-7xl mx-auto w-full pt-8 px-4">
      <section class="w-full pt-8">
        <div class="py-20 rounded-2xl flex items-center justify-center relative">
          <div class="text-center z-10">
            <h1 class="text-h1">Mes réservations</h1>
            <p class="mt-4">Consultez ou annulez vos réservations.</p>
          </div>
        </div>
      </section>

      <div v-if="pending" class="flex justify-center py-10">
        <span class="animate-spin w-8 h-8 border-4 border-gray-300 border-t-transparent rounded-full" />
      </div>
      <p v-else-if="error" class="text-center text-red-600">
        Erreur lors du chargement des réservations.
      </p>
      <p v-else-if="!bookings?.length" class="text-center text-gray-500">
        Aucune réservation trouvée.
      </p>

      <div v-else class="space-y-6">
        <BookingItem
            v-for="booking in bookings"
            :key="booking.id"
            :booking="booking"
            contact-label="Contacter l’hôte"
            delete-label="Annuler réservation"
            @delete="() => { selectedBookingId = booking.id; showCancelModal = true }"
            @contact="contactHost"
        />
      </div>
    </div>

    <UFooter />

    <div
        v-if="showCancelModal"
        class="fixed inset-0 bg-black/50 flex items-center justify-center z-50"
    >
      <div class="bg-white p-6 rounded-xl shadow-lg max-w-sm text-center space-y-4">
        <h2 class="text-xl font-bold text-red-600">Annuler cette réservation&nbsp;?</h2>
        <p class="text-gray-600">Êtes-vous sûr de vouloir supprimer votre réservation ?</p>
        <div class="flex justify-center gap-4 mt-4">
          <UButton size="md" variant="outline" @click="showCancelModal = false">Retour</UButton>
          <UButton size="md" variant="destructive" @click="cancelBooking">
            Confirmer
          </UButton>
        </div>
      </div>
    </div>
  </main>
</template>
