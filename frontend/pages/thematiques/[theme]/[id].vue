<script setup lang="ts">
    import '~/types/theme';
    import '~/types/accommodation';

    const comment = ref([]);
    const rental = [
        { title: 'Friends', image: '/friends.png' },
        { title: 'Star Wars', image: '/StarWars.png' },
        { title: 'Le Seigneur des anneaux', image: '/Seingeur_des_anneaux.png' },
    ];

    const { theme, id } = useRoute().params;
    const Location = ref<Accommodation | null>(null);

    onMounted(async () => {
        const { $api } = useNuxtApp();

        try {
            const [accommodationResponse, commentsResponse] = await Promise.all([
                useAuthFetch<any>($api(`/api/accommodations/${id}`)),
                useAuthFetch<any[]>($api(`/api/comments/accommodation/${id}`)),
            ]);

            if (accommodationResponse?.data?.value) {
                Location.value = accommodationResponse.data.value;
            } else {
                console.warn("Pas de données d'accommodation reçues");
            }

            if (commentsResponse?.data?.value) {
                comment.value = commentsResponse.data.value.map((c: any) => ({
                    id: c.id,
                    name: `${c.client.firstName || 'Client'} ${c.client.lastName || ''}`.trim(),
                    userDetail: 'PopnBeder depuis ' + (1 + Math.floor(Math.random() * 5)) + ' ans',
                    comment: c.content,
                    rating: c.rating,
                    userImage: 'https://via.placeholder.com/150',
                }));
            } else {
                console.warn('Aucun commentaire trouvé pour ce logement.');
            }
        } catch (error) {
            console.error('Erreur lors du chargement du logement ou des commentaires :', error);
        }
    });
</script>

<template>
    <main>
        <UHeader />
        <div class="max-w-7xl mx-auto w-full pt-8 px-4">
            <section id="rental-image" class="mb-20 pt-20">
                <h1 class="text-h1 mb-12 text-center">{{ Location?.name }}</h1>
                <CarouselRental :images="Location?.images || []" />
            </section>
            <div class="flex flex-col md:flex-row gap-8">
                <div class="flex-1 space-y-20">
                    <RentalInformation :informations="Location" :host="Location?.host" />
                    <RentalPraticalInformation :practical-infos="Location?.practicalInformations" />
                    <section id="comments">
                        <h2 class="text-h2 mb-6">Les commentaires</h2>
                        <p v-if="!comment.length" class="text-gray-500">Aucun commentaire pour le moment.</p>
                        <CommentCards v-else :items="comment" />
                    </section>
                </div>
                <div class="relative w-full md:w-1/3">
                    <div class="sticky top-24">
                        <BookingCard :price-per-night="Location?.price" />
                    </div>
                </div>
            </div>
            <section id="consult-trending" class="w-full pt-32">
                <h2 class="text-center text-h2 mb-10">Consultés récemment</h2>
                <RentalCards :items="rental" />
            </section>
        </div>
        <UFooter />
    </main>
</template>
