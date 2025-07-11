<script setup lang="ts">
    import { ref, onMounted } from 'vue';
    import BookingItem from '~/components/BookingItem.vue';
    import type { AccommodationDto } from '~/types/dtos/accommodation.dto';

    definePageMeta({
        ssr: false,
    });

    const { $api } = useNuxtApp();
    const router = useRouter();

    const bookings = ref([]);
    const pending = ref(true);
    const error = ref(null);
    const showCancelModal = ref(false);
    const selectedBookingId = ref<number | null>(null);

    async function loadBookings() {
        try {
            pending.value = true;
            error.value = null;

            const authStore = useAuthStore();

            if (!authStore.isAuthenticated) {
                router.push('/login');
                return;
            }

            const response = await useAuthFetch($api('/api/bookings/me'));
            const data = response.data?.value || response;

            bookings.value = Array.isArray(data)
                ? data.map((b: AccommodationDto) => ({
                      ...b,
                      startDate: new Date(b.startDate),
                      endDate: new Date(b.endDate),
                  }))
                : [];
        } catch (e) {
            console.error('Erreur chargement réservations:', e);
            error.value = e;
        } finally {
            pending.value = false;
        }
    }

    async function cancelBooking() {
        if (!selectedBookingId.value) return;
        try {
            await useAuthFetch($api(`/api/bookings/${selectedBookingId.value}`), {
                method: 'DELETE',
            });
            await loadBookings();
            showCancelModal.value = false;
        } catch (e) {
            console.error('Erreur annulation:', e);
            alert("Erreur lors de l'annulation.");
        }
    }

    function contactHost(bookingId: number) {
        alert(`Fonction à implémenter pour contacter l'hôte de la réservation #${bookingId}`);
    }

    onMounted(() => {
        loadBookings();
    });
</script>
<template>
    <main class="w-full h-full flex-grow flex flex-col">
        <UHeader />
        <div class="max-w-7xl mx-auto w-full pt-8 px-4 flex-grow">
            <section class="w-full pt-8">
                <div class="py-20 rounded-2xl flex items-center justify-center">
                    <div class="text-center">
                        <h1 class="text-h1">Mes réservations</h1>
                        <p class="mt-4">Consultez ou annulez vos réservations.</p>
                    </div>
                </div>
            </section>
            <div v-if="pending" class="flex justify-center py-10">
                <span class="animate-spin w-8 h-8 border-4 border-gray-300 border-t-transparent rounded-full" />
            </div>
            <div v-else-if="error" class="text-center py-10">
                <p class="text-red-600 mb-4">Une erreur est survenue lors du chargement</p>
                <Ubutton size="lg" variant="primary" @click="loadBookings()"> Réessayer </Ubutton>
            </div>
            <div v-else-if="!bookings?.length" class="text-center py-10 flex flex-col items-center">
                <p class="text-gray-500 mb-4">Aucune réservation trouvée</p>
                <UButton size="lg" variant="primary" @click="$router.push('/explorer')">
                    Explorer les logements
                </UButton>
            </div>
            <div v-else class="space-y-6">
                <BookingItem
                    v-for="booking in bookings"
                    :key="booking.id"
                    :booking="booking"
                    contact-label="Contacter l'hôte"
                    delete-label="Annuler réservation"
                    @delete="
                        () => {
                            selectedBookingId = booking.id;
                            showCancelModal = true;
                        }
                    "
                    @contact="contactHost"
                />
            </div>
        </div>
        <UFooter />
        <div v-if="showCancelModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
            <div class="bg-white p-6 rounded-xl shadow-lg max-w-sm text-center space-y-4">
                <h2 class="text-xl font-bold text-red-600">Annuler cette réservation ?</h2>
                <p class="text-gray-600">Êtes-vous sûr de vouloir supprimer votre réservation ?</p>
                <div class="flex justify-center gap-4 mt-4">
                    <UButton size="md" variant="outline" @click="showCancelModal = false"> Retour </UButton>
                    <UButton size="md" variant="destructive" @click="cancelBooking"> Confirmer </UButton>
                </div>
            </div>
        </div>
    </main>
</template>
