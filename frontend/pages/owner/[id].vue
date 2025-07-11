<script setup lang="ts">
    import AccommodationCards from '~/components/AccommodationCards.vue';
    import { useOwner } from '~/composables/useOwner';
    import { useConversationStore } from '~/stores/conversation';
    import { useAuthStore } from '~/stores/auth';
    import { useToast } from '~/composables/useToast';

    // Interface pour adapter les données du propriétaire au format attendu par OwnerInformationCard
    interface Host {
        name: string;
        image: string;
        verified: boolean;
        note: number;
        evaluations: number;
        experienceYears: number;
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
        getAverageRating,
        getFullName,
        getMembershipDuration,
    } = useOwner();

    const conversationStore = useConversationStore();
    const authStore = useAuthStore();
    const toast = useToast();
    const isCreatingConversation = ref(false);

    // Propriétaire adapté pour le composant OwnerInformationCard
    const hostData = computed<Host>(() => ({
        name: getFullName.value,
        image: 'https://via.placeholder.com/150', // Remplacer par l'image du propriétaire si disponible
        verified: true,
        note: getAverageRating.value,
        evaluations: comments.value?.length || 0,
        experienceYears: parseInt(getMembershipDuration.value) || 1,
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
                            <div class="flex items-center gap-3">
                                <img src="/work.svg" alt="Profession" class="w-8 h-8" />
                                <span class="font-medium"
                                    >Profession : {{ owner.profession || 'Gérant de Maple Hill Retreats' }}</span
                                >
                            </div>
                            <div class="flex items-center gap-3">
                                <img src="/world.svg" alt="Langues parlées" class="w-8 h-8" />
                                <span class="font-medium">Langues parlées : {{ owner.languages || 'Anglais' }}</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <img src="/location.svg" alt="Localisation" class="w-8 h-8" />
                                <span class="font-medium"
                                    >Je vis ici : {{ owner.adress || 'Elsworth, Royaume-Uni' }}</span
                                >
                            </div>
                            <div class="flex items-center gap-3">
                                <img src="" alt="Rôles" class="w-8 h-8" />
                                <span class="font-medium">Rôles : {{ owner.roles?.join(', ') }}</span>
                            </div>
                        </div>
                        <div class="text-gray-800 text-base leading-relaxed mt-6">
                            {{
                                owner.bio ||
                                'Je vis avec ma femme, Caroline, et nos deux enfants, Jasper (16 ans) et Emilia (13 ans). Nous sommes tombés amoureux de cette région paisible entourée de forêts et de collines douces. Le Cambridgeshire regorge de petits trésors : chemins de randonnée, marchés fermiers et jolis pubs campagnards. Nous adorons les activités en plein air comme le jardinage, les balades à vélo, le kayak et les soirées feu de camp.'
                            }}
                        </div>
                        <div class="mt-4">
                            <p><strong>Email :</strong> {{ owner.email }}</p>
                            <p v-if="owner.phone"><strong>Téléphone :</strong> {{ owner.phone }}</p>
                            <p>
                                <strong>Compte créé le :</strong> {{ new Date(owner.createdAt).toLocaleDateString() }}
                            </p>
                        </div>
                    </section>
                    <section id="owner-comment" class="space-y-6 pb-10 border-b border-gray-300">
                        <h2 class="text-3xl font-bold mb-6">Commentaires pour {{ owner.firstName }}</h2>
                        <CommentCards :items="comments" />
                        <p v-if="!comments || !comments.length" class="text-gray-500">Aucun commentaire disponible</p>
                        <a href="#" class="underline font-semibold mt-6 inline-block">Afficher les commentaires</a>
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
