<template>
    <div class="accommodation-image-manager">
        <!-- Affichage des images actuelles -->
        <div v-if="localImages.length > 0" class="mb-4">
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                <div v-for="(image, index) in localImages" :key="index" class="relative group">
                    <img :src="image.url" :alt="`Image ${index + 1}`" class="w-full h-32 object-cover rounded-lg" />
                    <div
                        v-if="image.isMain"
                        class="absolute top-2 left-2 bg-blue-500 text-white px-2 py-1 rounded text-xs"
                    >
                        Principale
                    </div>
                </div>
            </div>
        </div>

        <!-- Zone d'upload/gestion -->
        <div
            class="w-full h-40 rounded-2xl bg-gray-100 flex flex-col items-center justify-center cursor-pointer hover:bg-gray-200 border-dashed border-2 border-gray-300"
            @click="openModal"
        >
            <img src="/icon.svg" alt="Ajouter" class="w-10 h-10 opacity-40" />
            <p class="mt-2 text-sm text-gray-400 text-center">
                {{
                    localImages.length === 0
                        ? "Cliquez pour ajouter jusqu'à 10 images"
                        : `${localImages.length}/10 images - Cliquez pour gérer`
                }}
            </p>
        </div>

        <!-- Modal d'upload -->
        <AccommodationImageUploadModal
            :is-open="isModalOpen"
            :images="localImages"
            :accommodation-id="accommodationId"
            @close="closeModal"
            @images-updated="handleImagesUpdated"
        />
    </div>
</template>

<script setup lang="ts">
    interface ImageData {
        url: string;
        file?: File;
        isMain?: boolean;
    }

    interface Props {
        images?: ImageData[];
        accommodationId?: number;
    }

    interface Emits {
        (e: 'images-updated', images: ImageData[]): void;
    }

    const props = withDefaults(defineProps<Props>(), {
        images: () => [],
        accommodationId: undefined,
    });

    const emit = defineEmits<Emits>();

    const isModalOpen = ref(false);
    const localImages = ref<ImageData[]>([...(props.images || [])]);

    // Sync avec les props
    watch(
        () => props.images,
        (newImages) => {
            localImages.value = [...(newImages || [])];
        },
        { deep: true, immediate: true }
    );

    const openModal = () => {
        isModalOpen.value = true;
    };

    const closeModal = () => {
        isModalOpen.value = false;
    };

    const handleImagesUpdated = (updatedImages: ImageData[]) => {
        localImages.value = [...updatedImages];
        emit('images-updated', updatedImages);
    };
</script>

<style scoped>
    .accommodation-image-manager {
        @apply space-y-4;
    }
</style>
