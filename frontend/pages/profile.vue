<script setup>
    import PersonalInfoCard from '~/components/PersonalInfoCard.vue';
    import { useRecommendations } from '~/composables/useRecommendations';

    const authStore = useAuthStore();
    const recentlyViewedStore = useRecentlyViewedStore();
    const { getPersonalizedRecommendations } = useRecommendations();
    const user = authStore.user;
    const personalizedRecommendations = ref([]);

    useSeoMeta({
        title: () => (user?.firstName ? `Profil de ${user.firstName} - PopnBed` : 'Mon profil - PopnBed'),
        description: 'Gérez vos informations personnelles, vos biens publiés et vos suggestions personnalisées.',
    });

    onMounted(async () => {
        const recommendations = await getPersonalizedRecommendations();
        if (recommendations && recommendations.length > 0) {
            personalizedRecommendations.value = recommendations.map((item) => ({
                title: item.name,
                image: item.images && item.images.length > 0 ? item.images[0].url : '/placeholder-image.jpg',
                id: item.id,
                slug: item.theme?.slug || '',
                price: item.price,
            }));
        }
    });

    const recentlyViewed = computed(() => {
        return recentlyViewedStore.getRecentlyViewedExcept();
    });

    const userAccommodations = computed(() => {
        if (!user?.accommodations || !Array.isArray(user.accommodations)) {
            return [];
        }
        return user.accommodations.map((item) => ({
            title: item.name,
            image: item.images && item.images.length > 0 ? item.images[0].url : '/placeholder-image.jpg',
            id: item.id,
            slug: item.theme?.slug || '',
            price: item.price,
        }));
    });
</script>

<template>
    <main class="w-full">
        <UHeader />
        <div class="max-w-7xl w-full mx-auto pt-8 px-4">
            <section id="information-profile" class="w-full pt-8 mb-10">
                <div class="py-20 rounded-2xl flex items-center justify-center">
                    <div class="text-center">
                        <h1 class="text-h1">Mes informations personnelles</h1>
                    </div>
                </div>
                <PersonalInfoCard />
            </section>
            <section v-if="recentlyViewedStore.items.length > 0" id="client-rentals" class="mb-20 mt-20">
                <h2 class="text-center text-h2 mb-10">Consultés récemment</h2>
                <AccommodationCards :items="recentlyViewed" />
            </section>
            <section v-if="user?.accommodationCount && user.accommodationCount > 0" id="owner-rentals" class="mb-20">
                <h2 class="text-center text-h2 mb-10">Mes biens publiés</h2>
                <AccommodationCards :items="userAccommodations" />
            </section>
            <section v-if="personalizedRecommendations.length > 0" id="personalized-recommendations" class="mb-20">
                <h2 class="text-center text-h2 mb-10">Suggestions personnalisées</h2>
                <AccommodationCards :items="personalizedRecommendations" />
            </section>
        </div>
        <UFooter />
    </main>
</template>
