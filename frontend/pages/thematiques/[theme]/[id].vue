<script setup lang="ts">
    import '~/types/theme';
    import '~/types/accommodation';
    const comment = [
        {
            id: 1,
            name: 'Cyril',
            userDetail: 'PopnBeder depuis 3 ans',
            comment: 'Super séjour, Jean-François nous mets aux petits soins et la maison est incroyable !',
            userImage: 'https://via.placeholder.com/150',
            rating: 5,
        },
        {
            id: 2,
            name: 'Sophie',
            userDetail: 'PopnBeder depuis 5 ans',
            comment:
                'Un séjour parmis les orgres plus qu’agréable. Mes enfants été aux anges. La maison est fidèle au dessin...',
            userImage: 'https://via.placeholder.com/150',
            rating: 5,
        },
        {
            id: 3,
            name: 'Cyril',
            userDetail: 'PopnBeder depuis 3 ans',
            comment:
                'J’y ai amené mes petits enfants pour les vacances. Endroit douillé bien que les toilettes soient à l’extérieur.',
            userImage: 'https://via.placeholder.com/150',
            rating: 5,
        },
        {
            id: 4,
            name: 'Sam',
            userDetail: 'PopnBeder depuis 2 ans',
            comment: 'Grand fan de Shrek, c’est juste incroyable ! Et Jean-François est au top !',
            userImage: 'https://via.placeholder.com/150',
            rating: 5,
        },
    ];

    const rental = [
        { title: 'Friends', image: '/friends.png' },
        { title: 'Star Wars', image: '/StarWars.png' },
        { title: 'Le Seigneur des anneaux', image: '/Seingeur_des_anneaux.png' },
    ];
    const { theme, id } = useRoute().params;
    const Location = ref<Accommodation[]>([]);

    onMounted(async () => {
        const { $api } = useNuxtApp();
        try {
            const response = await useAuthFetch<Accommodation>($api('/api/accommodations/' + id));
            if (response) {
                Location.value = response.data.value.accommodation;
                Location.value.images = response.data.value.accommodation.images.map((image) => ({
                    url: image.url,
                    alt: image.alt || "Image de l'hébergement",
                    main: image.main || false,
                }));
            } else {
                Location.value = null;
            }
        } catch (error) {
            console.error('Erreur lors de la récupération du logement :', error);
            Location.value = null;
        }
    });
</script>

<template>
    <main>
        <UHeader />
        <div class="max-w-7xl mx-auto w-full pt-8 px-4">
            <section id="rental-image" class="mb-20 pt-20">
                <h1 class="text-h1 mb-12 text-center">{{ Location.name }}</h1>
                <CarouselRental :images="Location.images" />
            </section>
            <div class="flex flex-col md:flex-row gap-8">
                <div class="flex-1 space-y-20">
                    <RentalInformation />
                    <RentalPraticalInformation />
                    <section id="comments">
                        <h2 class="text-h2 mb-6">Les commentaires</h2>
                        <CommentCards :items="comment" />
                    </section>
                </div>
                <div class="relative w-full md:w-1/3">
                    <div class="sticky top-24">
                        <!-- <BookingCard /> -->
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
