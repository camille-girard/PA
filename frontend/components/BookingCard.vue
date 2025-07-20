<script setup lang="ts">
    import { ref, computed } from 'vue';
    import { useRouter } from 'vue-router';
    import { useAuthStore } from '~/stores/auth';
    import { useConversationStore } from '~/stores/conversation';
    import { useToast } from '~/composables/useToast';
    import UDatePicker from '~/components/molecules/UDatePicker.vue';
    import UInputNumber from '~/components/atoms/UInputNumber.vue';
    import UButton from '~/components/atoms/UButton.vue';

    interface Props {
        pricePerNight: number;
        accommodationId: number;
        title: string;
        ownerId?: number;
    }

    const props = defineProps<Props>();

    const arrivalDate = ref<Date | null>(null);
    const departureDate = ref<Date | null>(null);
    const amountTravelers = ref<number>(0);
    const showModal = ref(false);

    const router = useRouter();
    const auth = useAuthStore();
    const conversationStore = useConversationStore();
    const toast = useToast();
    const isCreatingConversation = ref(false);

    const numberOfNights = computed(() => {
        if (!arrivalDate.value || !departureDate.value) return 0;
        const diff = new Date(departureDate.value).getTime() - new Date(arrivalDate.value).getTime();
        const nights = diff / (1000 * 60 * 60 * 24);
        return nights > 0 ? nights : 0;
    });

    const total = computed(() => numberOfNights.value * props.pricePerNight);

    const handleBooking = () => {
        if (!arrivalDate.value || !departureDate.value || amountTravelers.value <= 0) {
            showModal.value = true;
            return;
        }

        if (!auth.isAuthenticated) {
            router.push('/login');
            return;
        }

        router.push({
            path: '/booking/booking-request',
            query: {
                title: props.title,
                arrival: arrivalDate.value.toISOString(),
                departure: departureDate.value.toISOString(),
                guests: amountTravelers.value.toString(),
                price: total.value.toFixed(2),
                pricePerNight: props.pricePerNight.toString(),
                nights: numberOfNights.value.toString(),
                accommodationId: props.accommodationId.toString(),
            },
        });
    };

    const createConversationWithOwner = async () => {
        if (!auth.isAuthenticated) {
            toast.error('Connexion requise', 'Vous devez être connecté pour contacter un propriétaire.');
            return router.push('/login');
        }

        if (!auth.user?.roles?.includes('ROLE_CLIENT')) {
            toast.error('Accès refusé', 'Seuls les clients peuvent contacter les propriétaires.');
            return;
        }

        if (!props.ownerId) {
            toast.error('Erreur', 'Impossible de contacter ce propriétaire.');
            return;
        }

        try {
            isCreatingConversation.value = true;
            const clientId = auth.user.id;
            const ownerId = props.ownerId;

            const conversation = await conversationStore.createConversation(clientId, ownerId);

            if (conversation && conversation.id) {
                toast.success('Conversation créée', 'Vous pouvez maintenant échanger avec ce propriétaire.');
                router.push(`/messages/${conversation.id}`);
            } else {
                throw new Error('Conversation invalide reçue');
            }
        } catch (error) {
            console.error('Erreur lors de la création de la conversation:', error);
            const errorMessage = error instanceof Error ? error.message : 'Impossible de créer une conversation avec ce propriétaire.';
            toast.error('Erreur', errorMessage);
        } finally {
            isCreatingConversation.value = false;
        }
    };
</script>

<template>
    <div class="w-full max-w-sm p-6 bg-white border border-gray-300 rounded-2xl shadow-sm space-y-4">
        <div class="text-center">
            <p class="font-bold text-lg">
                {{ props.pricePerNight }} € <span class="font-normal text-gray-500">/ nuit</span>
            </p>
        </div>

        <UDatePicker v-model="arrivalDate" placeholder="Arrivée" type="date" class="w-full" />
        <UDatePicker v-model="departureDate" placeholder="Départ" type="date" class="w-full" />
        <UInputNumber
            v-model="amountTravelers"
            placeholder="Nombre de voyageurs"
            :min="1"
            class="w-full"
            suffix="voyageur"
        />

        <div class="w-full rounded-md border border-gray-200 bg-gray-50 px-4 py-3 text-center">
            <p class="text-sm text-gray-500">Total du séjour</p>
            <p class="text-lg font-semibold text-gray-800">
                {{ total > 0 ? `${total} €` : '—' }}
            </p>
        </div>

        <div class="flex space-x-2 pt-2">
            <button
                class="w-1/2 bg-orange-500 hover:bg-orange-600 text-white font-semibold py-2 rounded-md"
                @click="handleBooking"
            >
                Réserver
            </button>
            <button 
                class="w-1/2 bg-black hover:bg-gray-800 text-white font-semibold py-2 rounded-md disabled:opacity-50 disabled:cursor-not-allowed"
                :disabled="isCreatingConversation"
                @click="createConversationWithOwner"
            >
                <span v-if="isCreatingConversation" class="animate-spin mr-1">⏳</span>
                {{ isCreatingConversation ? 'Contact...' : 'Contacter l\'hôte' }}
            </button>
        </div>
    </div>
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white p-6 rounded-xl shadow-lg max-w-sm text-center space-y-4">
            <h2 class="text-xl font-bold">Champs manquants</h2>
            <p class="text-gray-600">Merci de remplir les dates et le nombre de voyageurs avant de réserver.</p>
            <div class="flex justify-center">
                <UButton size="md" variant="primary" @click="showModal = false"> Fermer </UButton>
            </div>
        </div>
    </div>
</template>
