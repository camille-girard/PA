<script setup lang="ts">
    import AccommodationCards from '~/components/AccommodationCards.vue';
    import { useOwner } from '~/composables/useOwner';
    import { useConversationStore } from '~/stores/conversation';
    import { useAuthStore } from '~/stores/auth';
    import { useToast } from '~/composables/useToast';

    interface Host {
        name: string;
        image: string;
        verified: boolean;
        rating: number;
        evaluations: number;
        membershipDuration: string;
        verifications: string[];
    }

    const route = useRoute();
    const router = useRouter();
    const {
        owner,
        comments,
        rentals,
        isOwnerLoading,
        fetchOwnerById,
        getOwnerRating,
        getFullName,
        getMembershipDuration,
    } = useOwner();

    const conversationStore = useConversationStore();
    const authStore = useAuthStore();
    const toast = useToast();
    const isCreatingConversation = ref(false);

    const getShortAddress = computed(() => {
        if (!owner.value?.address) return '';

        const parts = owner.value.address.split(', ');
        if (parts.length >= 2) {
            return parts[1];
        }
        return owner.value.address;
    });

    const hostData = computed<Host>(() => ({
        name: getFullName.value,
        image: owner.value?.avatar || '',
        verified: true,
        rating: getOwnerRating.value,
        evaluations: comments.value?.length || 0,
        membershipDuration: getMembershipDuration.value,
        verifications: ['Email', 'Téléphone'],
    }));

    /**
     * Crée une conversation avec le propriétaire et redirige l'utilisateur vers celle-ci
     */
    const createConversationWithOwner = async () => {
        if (!authStore.isAuthenticated) {
            toast.error('Connexion requise', 'Vous devez être connecté pour contacter un propriétaire.');
            return router.push('/login');
        }

        if (!authStore.user?.roles?.includes('ROLE_CLIENT')) {
            toast.error('Accès refusé', 'Seuls les clients peuvent contacter les propriétaires.');
            return;
        }

        if (!owner.value?.id) {
            toast.error('Erreur', 'Impossible de contacter ce propriétaire.');
            return;
        }

        try {
            isCreatingConversation.value = true;
            const clientId = authStore.user.id;
            const ownerId = parseInt(owner.value.id);

            const conversation = await conversationStore.createConversation(clientId, ownerId);

            if (conversation) {
                toast.success('Conversation créée', 'Vous pouvez maintenant échanger avec ce propriétaire.');
                router.push(`/messages/${conversation.id}`);
            }
        } catch (error) {
            console.error('Erreur lors de la création de la conversation:', error);
            toast.error('Erreur', 'Impossible de créer une conversation avec ce propriétaire.');
        } finally {
            isCreatingConversation.value = false;
        }
    };

    onMounted(async () => {
        if (typeof route.params.id === 'string') {
            await fetchOwnerById(route.params.id);
        }
    });

    useSeoMeta({
        title: () => (owner.value ? `${getFullName.value} - PopnBed` : 'Propriétaire - PopnBed'),
        description: () =>
            owner.value
                ? `Découvrez ${getFullName.value}, un propriétaire passionné par l'hospitalité et la nature. Contactez-le pour en savoir plus sur ses hébergements uniques.`
                : "Découvrez nos propriétaires passionnés par l'hospitalité et la nature.",
    });
</script>

<template>
    <main>
        <UHeader />
        <div class="max-w-7xl mx-auto w-full pt-32 px-4">
            <div v-if="owner" class="flex flex-col md:flex-row gap-10">
                <aside class="md:w-1/3 space-y-8">
                    <OwnerInformationCard :host="hostData" />

                    <div class="bg-white rounded-2xl shadow-md p-6">
                        <h3 class="font-semibold text-lg mb-4">Contacter {{ owner.firstName }}</h3>
                        <p class="text-gray-600 dark:text-gray-300 text-sm mb-4">
                            Vous avez des questions sur ses hébergements ? Contactez directement {{ owner.firstName }} !
                        </p>
                        <UButton class="w-full" :disabled="isCreatingConversation" @click="createConversationWithOwner">
                            <span v-if="isCreatingConversation" class="animate-spin mr-2">⏳</span>
                            {{ isCreatingConversation ? 'Création...' : 'Contacter le propriétaire' }}
                        </UButton>
                    </div>
                </aside>
                <section class="md:w-2/3 space-y-16">
                    <section id="owner-information" class="space-y-6 pb-10 border-b border-gray-300">
                        <h2 class="text-3xl font-bold mb-6">Informations sur {{ owner.firstName }}</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-gray-700">
                            <div v-if="getOwnerRating >= 4" class="flex items-center gap-3">
                                <img src="/badge_icon.svg" alt="Badge Super Hote" class="w-8 h-8" />
                                <span class="font-medium">Qualification : Super Hôte</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <img src="/location.svg" alt="Localisation" class="w-8 h-8" />
                                <span class="font-medium">Je vis ici : {{ getShortAddress }}</span>
                            </div>
                        </div>
                        <div class="text-gray-800 text-base leading-relaxed mt-6">
                            {{
                                owner.bio ||
                                'Je vis avec ma femme, Caroline, et nos deux enfants, Jasper (16 ans) et Emilia (13 ans). Nous sommes tombés amoureux de cette région paisible entourée de forêts et de collines douces. Le Cambridgeshire regorge de petits trésors : chemins de randonnée, marchés fermiers et jolis pubs campagnards. Nous adorons les activités en plein air comme le jardinage, les balades à vélo, le kayak et les soirées feu de camp.'
                            }}
                        </div>
                    </section>
                    <section id="owner-rentals" class="space-y-6">
                        <h2 class="text-3xl font-bold mb-10">Annonces publiées par {{ owner.firstName }}</h2>
                        <AccommodationCards :items="rentals" />
                        <p v-if="!rentals.length" class="text-gray-500">Aucun hébergement disponible</p>
                    </section>
                </section>
            </div>
            <div v-else class="py-10 text-center">
                <div v-if="isOwnerLoading" class="flex flex-col items-center justify-center space-y-4">
                    <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-primary"></div>
                    <p class="text-lg text-gray-700">Chargement des informations du propriétaire...</p>
                </div>
                <div v-else class="text-red-500">
                    <p class="text-xl">Impossible de charger les informations du propriétaire</p>
                    <p class="mt-2">Veuillez réessayer ultérieurement ou contacter le support</p>
                </div>
            </div>
        </div>
        <UFooter />
    </main>
</template>
