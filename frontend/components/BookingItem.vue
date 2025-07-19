<script setup lang="ts">
    import type { Booking } from '~/types/booking';
    import RatingModal from '~/components/RatingModal.vue';

    const props = defineProps<{
        booking: Booking;
        contactLabel?: string;
        deleteLabel?: string;
        showRatingButton?: boolean;
    }>();

    const emit = defineEmits(['delete', 'contact', 'rate']);

    const showRatingModal = ref(false);

    const formatDate = (d: string) => {
        const date = new Date(d);
        return date.toLocaleDateString('fr-FR', {
            day: '2-digit',
            month: 'short',
            year: 'numeric',
        });
    };

    function onDelete(id: number) {
        emit('delete', id);
    }

    function onContact(id: number) {
        emit('contact', id);
    }

    function onRate() {
        showRatingModal.value = true;
    }

    function calculateNights(startDate: string, endDate: string): number {
        const start = new Date(startDate).getTime();
        const end = new Date(endDate).getTime();
        return Math.round((end - start) / (1000 * 60 * 60 * 24));
    }

    const isBookingCompleted = computed(() => {
        const endDate = new Date(props.booking.endDate);
        const now = new Date();
        return endDate < now && props.booking.status === 'accepted';
    });

    const isBookingFuture = computed(() => {
        const startDate = new Date(props.booking.startDate);
        const now = new Date();
        return startDate > now;
    });

    const handleRatingSubmit = async (ratingData: {
        accommodationRating: number;
        accommodationComment: string;
        ownerRating: number;
    }) => {
        try {
            const { $api } = useNuxtApp();
            const response = await useAuthFetch($api(`/api/bookings/${props.booking.id}/rate`), {
                method: 'POST',
                body: JSON.stringify(ratingData),
                headers: {
                    'Content-Type': 'application/json',
                },
            });

            if (response.error.value) {
                console.error('Erreur lors de la notation:', response.error.value);
                return;
            }

            showRatingModal.value = false;

            emit('rate', { bookingId: props.booking.id, ratingData, hasRated: true });
        } catch (error) {
            console.error('Erreur lors de la soumission de la notation:', error);
        }
    };
</script>

<template>
    <div class="border border-gray-200 rounded-xl p-6 flex flex-col md:flex-row md:items-center md:justify-between">
        <div class="space-y-1">
            <h3 class="text-xl font-semibold">
                {{ booking.accommodation?.name ?? `Hébergement #${booking.accommodation?.id}` }}
            </h3>
            <p class="text-gray-600">
                Du <strong>{{ formatDate(booking.startDate) }}</strong> au
                <strong>{{ formatDate(booking.endDate) }}</strong> ({{
                    calculateNights(booking.startDate, booking.endDate)
                }}
                nuits)
            </p>
            <p v-if="booking.client" class="text-sm text-gray-500">
                Par : {{ booking.client.firstName }} {{ booking.client.lastName }}
            </p>
            <div class="flex items-center gap-1">
                <span class="font-medium">Statut :</span>
                <span
                    :class="{
                        'text-green-600': booking.status === 'accepted',
                        'text-yellow-600': booking.status === 'pending',
                        'text-red-600': ['cancelled', 'rejected'].includes(booking.status),
                    }"
                >
                    {{ booking.status }}
                </span>
            </div>
        </div>
        <div class="mt-4 md:mt-0 md:text-right space-y-2">
            <p class="text-lg font-bold">{{ booking.totalPrice.toFixed(2) }} €</p>
            <div class="flex flex-col md:flex-row md:justify-end gap-2">
                <UButton @click="onContact(booking.id)">
                    {{ contactLabel }}
                </UButton>
                <UButton v-if="showRatingButton && isBookingCompleted && !booking.hasRated" color="orange" @click="onRate()">
                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"
                        />
                    </svg>
                    Noter le séjour
                </UButton>
                <UButton v-else-if="showRatingButton && isBookingCompleted && booking.hasRated" color="green" disabled>
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Déjà noté
                </UButton>
                <UButton v-else-if="isBookingFuture" color="red" @click="onDelete(booking.id)">
                    {{ deleteLabel }}
                </UButton>
            </div>
        </div>
    </div>
    <RatingModal
        v-if="showRatingModal"
        :booking="booking"
        :is-visible="showRatingModal"
        @close="showRatingModal = false"
        @submit="handleRatingSubmit"
    />
</template>
