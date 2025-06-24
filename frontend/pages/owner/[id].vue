<script setup lang="ts">
    import '~/types/owner';
    import '~/types/comment';

    type Rental = {
        id: string;
        title: string;
        description: string;
    };

    const route = useRoute();
    const owner = ref<Owner | null>(null);
    const comments = ref<Comment[]>([]);
    const rentals = ref<Rental[]>([]);

    onMounted(async () => {
        const { $api } = useNuxtApp();
        try {
            const response = await useAuthFetch<Owner>($api('/api/owners/' + route.params.id));

            if (response.data.value) {
                owner.value = response.data.value;
                comments.value = response.data.value.comments || [];

                if (response.data.value.accommodations) {
                    rentals.value = response.data.value.accommodations.map((accommodation) => ({
                        title: accommodation.name,
                        image: accommodation.images?.[0]?.url || 'https://via.placeholder.com/400x250',
                        description: accommodation.description,
                        id: accommodation.id,
                        slug: accommodation.theme?.slug || 'default-slug',
                    }));
                }
            }
        } catch (error) {
            console.error('Erreur lors de la récupération des données du propriétaire :', error);
        }
    });
</script>

<template>
    <main>
        <UHeader />
        <div class="max-w-7xl mx-auto w-full pt-32 px-4">
            <div v-if="owner" class="flex flex-col md:flex-row gap-10">
                <aside class="md:w-1/3 space-y-8">
                    <OwnerInformationCard :host="owner" />
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
                        <p v-if="!comments.length" class="text-gray-500">Aucun commentaire disponible</p>
                        <a href="#" class="underline font-semibold mt-6 inline-block">Afficher les commentaires</a>
                    </section>
                    <section id="owner-rentals" class="space-y-6">
                        <h2 class="text-3xl font-bold mb-10">Annonces publiées par {{ owner.firstName }}</h2>
                        <LocationCards :items="rentals" />
                        <p v-if="!rentals.length" class="text-gray-500">Aucun hébergement disponible</p>
                    </section>
                </section>
            </div>
            <div v-else class="py-10 text-center">
                <p>Chargement des informations du propriétaire...</p>
            </div>
        </div>
        <UFooter />
    </main>
</template>
