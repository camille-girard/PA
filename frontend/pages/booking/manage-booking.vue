<script setup lang="ts">
    import { ref, computed } from 'vue';
    import { useRouter } from 'vue-router';
    import { useAuthStore } from '~/stores/auth';
    import BookingItem from '~/components/BookingItem.vue';

    const router = useRouter();
    const auth = useAuthStore();
    if (!auth.isAuthenticated || !auth.user?.roles?.includes('ROLE_OWNER')) {
        router.push('/');
    }

    const { $api } = useNuxtApp();
    const selectedStatus = ref<'all' | 'pending' | 'accepted' | 'cancelled' | 'rejected'>('all');
    const showDeleteModal = ref(false);
    const selectedBookingId = ref<number | null>(null);

    const {
        data: bookings,
        pending,
        error,
        refresh: refreshBookings,
    } = await useAsyncData('ownerBookings', async () => {
        const data = await $fetch($api('/api/bookings/owner'), { credentials: 'include' });
        return data.map((b: AccommodationDto) => ({
            ...b,
            startDate: new Date(b.startDate),
            endDate: new Date(b.endDate),
        }));
    });

    const filteredBookings = computed(() => {
        if (!bookings.value) return [];
        return selectedStatus.value === 'all'
            ? bookings.value
            : bookings.value.filter((b) => b.status === selectedStatus.value);
    });

    async function deleteBooking() {
        if (!selectedBookingId.value) return;
        try {
            await $fetch($api(`/api/bookings/${selectedBookingId.value}`), {
                method: 'DELETE',
                credentials: 'include',
            });
            showDeleteModal.value = false;
            await refreshBookings();
        } catch (e) {
            console.error(e);
            alert('Erreur lors de la suppression.');
        }
    }

    function contactClient(bookingId: number) {
        alert(`Fonction à implémenter pour contacter le locataire #${bookingId}`);
    }
</script>

<template>
    <main class="w-full h-full">
        <UHeader />
        <div class="max-w-7xl w-full mx-auto pt-8 px-4">
            <section class="w-full pt-8">
                <div class="py-20 rounded-2xl flex items-center justify-center">
                    <div class="text-center">
                        <h1 class="text-h1">Réservations reçues</h1>
                        <p class="mt-4">Gérez les réservations de vos hébergements.</p>
                    </div>
                </div>
            </section>

            <div class="mb-8 flex flex-wrap gap-4 items-center">
                <label class="font-medium">Filtrer par statut&nbsp;:</label>
                <select v-model="selectedStatus" class="border px-4 py-2 rounded-md">
                    <option value="all">Tous</option>
                    <option value="pending">En attente</option>
                    <option value="accepted">Accepté</option>
                    <option value="cancelled">Annulé</option>
                    <option value="rejected">Rejeté</option>
                </select>
            </div>

            <div v-if="pending" class="flex justify-center py-10">
                <span class="animate-spin w-8 h-8 border-4 border-gray-300 border-t-transparent rounded-full" />
            </div>
            <p v-else-if="error" class="text-center text-red-600">Erreur lors du chargement des réservations.</p>
            <p v-else-if="!filteredBookings.length" class="text-center text-gray-500">Aucune réservation trouvée.</p>

            <div v-else class="space-y-6">
                <BookingItem
                    v-for="booking in filteredBookings"
                    :key="booking.id"
                    :booking="booking"
                    contact-label="Contacter le locataire"
                    delete-label="Supprimer la réservation"
                    @delete="
                        () => {
                            selectedBookingId = booking.id;
                            showDeleteModal = true;
                        }
                    "
                    @contact="contactClient"
                />
            </div>
        </div>

        <UFooter />

        <div v-if="showDeleteModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
            <div class="bg-white p-6 rounded-xl shadow-lg max-w-sm text-center space-y-4">
                <h2 class="text-xl font-bold text-red-600">Supprimer cette réservation ?</h2>
                <p class="text-gray-600">Cette action est irréversible.</p>
                <div class="flex justify-center gap-4 mt-4">
                    <UButton size="md" variant="outline" @click="showDeleteModal = false">Retour</UButton>
                    <UButton size="md" variant="destructive" @click="deleteBooking"> Confirmer </UButton>
                </div>
            </div>
        </div>
    </main>
</template>
