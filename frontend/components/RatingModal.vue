<script setup lang="ts">
    import type { Booking } from '~/types/booking';

    const props = defineProps<{
        booking: Booking;
        isVisible: boolean;
    }>();

    const emit = defineEmits<{
        'close': [];
        'submit': [data: { accommodationRating: number; accommodationComment: string; ownerRating: number }];
    }>();

    const accommodationRating = ref(0);
    const accommodationComment = ref('');
    const ownerRating = ref(0);
    const isSubmitting = ref(false);

    const canSubmit = computed(() => {
        return accommodationRating.value > 0 && accommodationComment.value.trim().length > 0 && ownerRating.value > 0;
    });

    const resetForm = () => {
        accommodationRating.value = 0;
        accommodationComment.value = '';
        ownerRating.value = 0;
        isSubmitting.value = false;
    };

    const submitRating = async () => {
        if (!canSubmit.value) {
            return;
        }

        isSubmitting.value = true;

        try {
            emit('submit', {
                accommodationRating: accommodationRating.value,
                accommodationComment: accommodationComment.value,
                ownerRating: ownerRating.value
            });
            
            resetForm();
        } catch (error) {
            isSubmitting.value = false;
        }
    };

    const closeModal = () => {
        resetForm();
        emit('close');
    };

    const formatDate = (dateString: string) => {
        return new Date(dateString).toLocaleDateString('fr-FR', {
            day: 'numeric',
            month: 'long',
            year: 'numeric'
        });
    };

    const selectAccommodationStar = (star: number) => {
        accommodationRating.value = star;
    };

    const selectOwnerStar = (star: number) => {
        ownerRating.value = star;
    };
</script>

<template>
    <div v-if="isVisible" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <!-- Header -->
            <div class="sticky top-0 bg-white border-b border-gray-200 p-6 rounded-t-xl">
                <div class="flex items-center justify-between">
                    <h2 class="text-2xl font-bold text-gray-900">Donnez votre avis</h2>
                    <button @click="closeModal" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
            <div class="p-6 space-y-6">
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="flex items-center gap-4">
                        <img 
                            :src="booking.accommodation.images?.[0]?.url || '/placeholder-accommodation.jpg'" 
                            :alt="booking.accommodation.name"
                            class="w-16 h-16 rounded-lg object-cover"
                        />
                        <div>
                            <h3 class="font-semibold text-lg">{{ booking.accommodation.name }}</h3>
                            <p class="text-gray-600">
                                Du {{ formatDate(booking.startDate) }} au {{ formatDate(booking.endDate) }}
                            </p>
                        </div>
                    </div>
                </div>
               <div class="space-y-4">
                    <h3 class="text-lg font-semibold flex items-center gap-2">
                        <svg class="w-5 h-5 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        Comment était le logement ?
                    </h3>
                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Note du logement</label>
                            <div class="flex items-center gap-1">
                                <button
                                    v-for="star in 5"
                                    :key="star"
                                    type="button"
                                    :class="[
                                        'w-6 h-6 transition-colors cursor-pointer hover:scale-110',
                                        star <= accommodationRating ? 'text-orange-500' : 'text-gray-300'
                                    ]"
                                    @click="selectAccommodationStar(star)"
                                >
                                    <svg fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.293c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.176 0l-2.8 2.034c-.784.57-1.838-.197-1.54-1.118l1.07-3.293a1 1 0 00-.364-1.118L2.98 8.719c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                    </svg>
                                </button>
                                <span v-if="accommodationRating > 0" class="ml-2 text-sm text-gray-600">{{ accommodationRating }}/5</span>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Votre commentaire *
                            </label>
                            <textarea
                                v-model="accommodationComment"
                                placeholder="Partagez votre expérience : propreté, équipements, emplacement..."
                                rows="4"
                                maxlength="500"
                                class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 resize-none"
                            ></textarea>
                            <p class="text-sm text-gray-500 mt-1">{{ accommodationComment.length }}/500 caractères</p>
                        </div>
                    </div>
                </div>
               <div class="space-y-4">
                    <h3 class="text-lg font-semibold flex items-center gap-2">
                        <svg class="w-5 h-5 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        Comment était votre hôte {{ booking.accommodation.owner.firstName }} ?
                    </h3>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Note de l'hôte</label>
                        <div class="flex items-center gap-1">
                            <button
                                v-for="star in 5"
                                :key="star"
                                type="button"
                                :class="[
                                    'w-6 h-6 transition-colors cursor-pointer hover:scale-110',
                                    star <= ownerRating ? 'text-orange-500' : 'text-gray-300'
                                ]"
                                @click="selectOwnerStar(star)"
                            >
                                <svg fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.293c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.176 0l-2.8 2.034c-.784.57-1.838-.197-1.54-1.118l1.07-3.293a1 1 0 00-.364-1.118L2.98 8.719c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                </svg>
                            </button>
                            <span v-if="ownerRating > 0" class="ml-2 text-sm text-gray-600">{{ ownerRating }}/5</span>
                        </div>
                        <p class="text-sm text-gray-500 mt-1">Communication, accueil, disponibilité...</p>
                    </div>
                </div>
            </div>
           <div class="sticky bottom-0 bg-white border-t border-gray-200 p-6 rounded-b-xl">
                <div class="flex gap-3 justify-end">
                    <button
                        @click="closeModal"
                        :disabled="isSubmitting"
                        class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors disabled:opacity-50"
                    >
                        Annuler
                    </button>
                    <button
                        @click="submitRating"
                        :disabled="!canSubmit || isSubmitting"
                        class="px-6 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
                    >
                        <span v-if="isSubmitting" class="animate-spin">⏳</span>
                        {{ isSubmitting ? 'Publication...' : 'Publier mon avis' }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>