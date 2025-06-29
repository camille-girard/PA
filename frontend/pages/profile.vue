<script setup>
    import RentalCards from '~/components/RentalCards.vue';
    import PersonalInfoCard from '~/components/PersonalInfoCard.vue';

    const authStore = useAuthStore();
    const recentlyViewedStore = useRecentlyViewedStore();
    const user = authStore.user;

    const recentlyViewed = computed(() => {
        return recentlyViewedStore.items.map((item) => ({
            title: item.name,
            image: item.images && item.images.length > 0 ? item.images[0].url : '/placeholder-image.jpg',
            id: item.id,
            slug: item.id.toString(),
            price: item.price,
        }));
    });
    console.log(user.accommodations);
    const userAccommodations = computed(() => {
        if (!user?.accommodations || !Array.isArray(user.accommodations)) {
            return [];
        }
        return user.accommodations.map((item) => ({
            title: item.name,
            image: item.images && item.images.length > 0 ? item.images[0].url : '/placeholder-image.jpg',
            id: item.id,
            slug: item.id.toString(),
            price: item.price,
        }));
    });
    console.log(userAccommodations);
</script>

<template>
    <main>
        <UHeader />
        <div class="max-w-7xl w-full mx-auto pt-8 px-4">
            <section id="information-profile" class="w-full pt-8">
                <div class="py-20 rounded-2xl flex items-center justify-center relative">
                    <div class="text-center z-10">
                        <h1 class="text-h1">Mes informations personnelles</h1>
                    </div>
                </div>
                <PersonalInfoCard />
            </section>
            <section id="client-rentals" class="mb-20 mt-20">
                <h2 class="text-center text-h2 mb-10">Consultés récemment</h2>
                <RentalCards :items="recentlyViewed" />
            </section>
            <section v-if="user?.accommodationCount && user.accommodationCount > 0" id="owner-rentals" class="mb-20">
                <h2 class="text-center text-h2 mb-10">Mes biens publiés</h2>
                <RentalCards :items="userAccommodations" />
            </section>
        </div>
        <UFooter />
    </main>
</template>
