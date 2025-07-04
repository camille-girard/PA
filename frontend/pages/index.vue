<script setup lang="ts">
    import '~/types/theme';
    useSeoMeta({
        title: "PopnBed - Un site de réservation d'hébergements inspirés de films",
        description: "PopnBed - Un site de réservation d'hébergements inspirés de films",
    });

    const trending = ref<Theme[]>([]);

    onMounted(async () => {
        const { $api } = useNuxtApp();
        const response = await useAuthFetch<Theme>($api('/api/themes/'));

        trending.value = response.data.value.themes.map((theme) => ({
            title: theme.name,
            image: theme.image,
            description: theme.description,
            slug: theme.slug,
        }));
    });
</script>

<template>
    <main class="w-full h-full">
        <UHeader />
        <div class="max-w-7xl w-full mx-auto pt-8 px-4">
            <section class="w-full pt-8">
                <div class="py-20 rounded-2xl flex items-center justify-center relative">
                    <div class="text-center z-10">
                        <h1 class="text-h1">Séjourner dans un lieu inspiré de films</h1>
                        <p class="mt-4">Trouver et réservez des hébergements uniques à thème cinématographique</p>
                        <SearchBar />
                    </div>
                </div>
            </section>
            <section class="w-full pt-12">
                <div class="text-center mb-10">
                    <h2 class="text-h2">Les tendances du moment</h2>
                </div>
                <RentalCards :items="trending" link-prefix="thematiques" />
                <div class="mt-10 text-center">
                    <NuxtLink to="/tendances">
                        <UButton class="mx-auto">Voir plus</UButton>
                    </NuxtLink>
                </div>
            </section>
            <section class="w-full pt-12">
                <div class="text-center mb-10">
                    <h2 class="text-h2">Parcourez un lieu unique digne d’un film ou d’une série culte</h2>
                </div>
                <div class="flex justify-center">
                    <MapBox />
                </div>
                <div class="mt-10 text-center">
                    <NuxtLink to="/explorer">
                        <UButton class="mx-auto">Explorez</UButton>
                    </NuxtLink>
                </div>
            </section>
        </div>
        <UFooter />
    </main>
</template>
