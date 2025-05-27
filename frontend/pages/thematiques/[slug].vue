<script setup lang="ts">
    import '~/types/logement';
    import '~/types/theme';

    const Theme = ref<Theme[]>([]);

    onMounted(async () => {
        const { $api } = useNuxtApp();
        const route = useRoute();

        const response = await useAuthFetch<Theme>($api('/api/themes/accommodations/' + route.params.slug));

        Theme.value = response.data.value.themes;
        Theme.value.accommodations = response.data.value.themes.accommodations.map((accommodation) => ({
            title: accommodation.name,
            image: accommodation.images[0].url,
            id: accommodation.id,
            slug: response.data.value.themes.slug,
        }));
    });
</script>

<template>
    <main>
        <UHeader />
        <div class="max-w-7xl w-full mx-auto pt-8 px-4">
            <section class="w-full pt-8">
                <div class="py-20 rounded-2xl flex items-center justify-center relative">
                    <div class="text-center z-10">
                        <h1 class="text-h1">Thématiques - {{ Theme.name }}</h1>
                        <p class="text-body-md mt-4">
                            Trouvez le lieu parfait inspiré de vos films et séries préférés.
                        </p>
                    </div>
                </div>
            </section>
            <section class="w-full pt-12">
                <div class="text-center mb-10">
                    <h2 class="text-h2">{{ Theme.name }}</h2>
                </div>
                <locationCards :items="Theme.accommodations" />
            </section>
        </div>
        <UFooter />
    </main>
</template>
