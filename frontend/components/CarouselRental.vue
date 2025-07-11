<script setup lang="ts">
    import { computed } from 'vue';

    interface CarouselImage {
        url: string;
        alt: string;
        main?: boolean;
    }

    const props = defineProps<{
        images?: CarouselImage[];
    }>();

    const sortedImages = computed(() =>
        (props.images ?? []).slice().sort((a, b) => (b.main ? 1 : 0) - (a.main ? 1 : 0))
    );
</script>

<template>
    <section class="max-w-7xl mx-auto px-4 py-10">
        <div v-if="sortedImages.length" class="flex flex-col md:flex-row gap-8 w-full mx-auto">
            <div class="flex-1">
                <img
                    :src="sortedImages[0].url"
                    :alt="sortedImages[0].alt"
                    class="w-full h-[500px] object-cover rounded-3xl shadow-lg hover:scale-105 transition-all duration-300"
                />
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 flex-1">
                <div
                    v-for="(image, index) in sortedImages.slice(1)"
                    :key="index"
                    class="relative group overflow-hidden rounded-2xl shadow-md hover:shadow-lg transition-all duration-300"
                >
                    <img
                        :src="image.url"
                        :alt="image.alt"
                        class="w-full h-40 object-cover group-hover:scale-105 transition-transform duration-300"
                    />
                    <div
                        class="absolute inset-0 bg-black/10 opacity-0 group-hover:opacity-100 transition duration-300 rounded-2xl"
                    ></div>
                </div>
            </div>
        </div>
        <div v-else>Aucune image disponible.</div>
    </section>
</template>
