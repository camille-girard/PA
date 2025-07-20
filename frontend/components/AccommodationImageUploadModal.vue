<template>
    <UBaseModal :is-open="isOpen" class="max-w-4xl" @close="handleClose">
        <div class="px-6 pt-6">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-blue-50 rounded-lg">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"
                        />
                    </svg>
                </div>
                <div>
                    <h3 class="text-xl font-semibold text-gray-900">Gérer les images</h3>
                    <p class="text-sm text-gray-500">Ajoutez jusqu'à 10 images pour votre logement</p>
                </div>
            </div>

            <div class="space-y-8 mt-6">
                <!-- Images actuelles -->
                <div v-if="currentImages.length > 0" class="space-y-4">
                    <div class="flex items-center justify-between">
                        <h4 class="text-lg font-medium text-gray-900">Images actuelles</h4>
                        <span
                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800"
                        >
                            {{ currentImages.length }}/10
                        </span>
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                        <div
                            v-for="(image, index) in currentImages"
                            :key="index"
                            class="relative group aspect-square bg-gray-100 rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-shadow duration-200"
                        >
                            <img :src="image.url" :alt="`Image ${index + 1}`" class="w-full h-full object-cover" />

                            <!-- Overlay avec actions - toujours visible sur mobile, hover sur desktop -->
                            <div
                                class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-100 md:opacity-0 md:group-hover:opacity-100 transition-opacity duration-200"
                            >
                                <div class="absolute bottom-2 left-2 right-2 flex gap-2">
                                    <button
                                        v-if="!image.isMain"
                                        :disabled="isUploading"
                                        class="flex-1 bg-white/90 hover:bg-white text-gray-900 px-3 py-1.5 rounded-lg text-xs font-medium transition-colors disabled:opacity-50 backdrop-blur-sm"
                                        @click="setMainImage(index)"
                                    >
                                        <svg
                                            class="w-3 h-3 inline mr-1"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"
                                            />
                                        </svg>
                                        Principale
                                    </button>
                                    <button
                                        :disabled="isUploading"
                                        class="bg-red-500 hover:bg-red-600 text-white p-1.5 rounded-lg transition-colors disabled:opacity-50"
                                        @click="removeImage(index)"
                                    >
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                                            />
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <!-- Badge image principale -->
                            <div
                                v-if="image.isMain"
                                class="absolute top-2 left-2 bg-blue-500 text-white px-2 py-1 rounded-lg text-xs font-medium shadow-lg flex items-center gap-1"
                            >
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"
                                    />
                                </svg>
                                Principale
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Zone d'upload améliorée -->
                <div class="space-y-4">
                    <h4 class="text-lg font-medium text-gray-900">Ajouter des images</h4>

                    <div class="relative">
                        <div
                            class="border-2 border-dashed border-gray-300 rounded-2xl p-8 text-center hover:border-blue-400 hover:bg-blue-50/50 transition-colors duration-200"
                        >
                            <UFileInput
                                ref="fileInput"
                                multiple
                                accept="image/*"
                                :disabled="isUploading || currentImages.length >= 10"
                                @update:file="handleFileSelect"
                            />
                        </div>

                        <div
                            v-if="currentImages.length >= 10"
                            class="mt-3 p-3 bg-yellow-50 border border-yellow-200 rounded-lg"
                        >
                            <div class="flex items-center gap-2 text-yellow-800">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.314 16.5c-.77.833.192 2.5 1.732 2.5z"
                                    />
                                </svg>
                                <span class="text-sm font-medium">Limite atteinte</span>
                            </div>
                            <p class="text-sm text-yellow-700 mt-1">
                                Vous avez atteint la limite de 10 images par logement
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Prévisualisation des nouvelles images -->
                <div v-if="pendingImages.length > 0" class="space-y-4">
                    <div class="flex items-center justify-between">
                        <h4 class="text-lg font-medium text-gray-900">Nouvelles images</h4>
                        <span
                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800"
                        >
                            {{ pendingImages.length }} en attente
                        </span>
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                        <div
                            v-for="(image, index) in pendingImages"
                            :key="index"
                            class="relative group aspect-square bg-gray-100 rounded-xl overflow-hidden shadow-sm border-2 border-dashed border-green-300"
                        >
                            <img
                                :src="image.preview"
                                :alt="`Nouvelle image ${index + 1}`"
                                class="w-full h-full object-cover"
                            />

                            <!-- Badge "Nouveau" -->
                            <div
                                class="absolute top-2 left-2 bg-green-500 text-white px-2 py-1 rounded-lg text-xs font-medium shadow-lg"
                            >
                                Nouveau
                            </div>

                            <!-- Bouton supprimer -->
                            <button
                                :disabled="isUploading"
                                class="absolute top-2 right-2 bg-red-500 hover:bg-red-600 text-white rounded-full w-7 h-7 flex items-center justify-center text-sm transition-colors disabled:opacity-50 shadow-lg"
                                @click="removePendingImage(index)"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"
                                    />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Barre de progression améliorée -->
                <div v-if="isUploading" class="space-y-3">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <svg
                                class="w-5 h-5 text-blue-600 animate-spin"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"
                                />
                            </svg>
                            <span class="text-sm font-medium text-gray-900">Upload en cours...</span>
                        </div>
                        <span class="text-sm text-gray-500">{{ progress }}%</span>
                    </div>

                    <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                        <div
                            class="bg-gradient-to-r from-blue-500 to-blue-600 h-2 rounded-full transition-all duration-500 ease-out"
                            :style="{ width: progress + '%' }"
                        />
                    </div>
                </div>
            </div>
        </div>

        <div class="pt-8">
            <div class="flex justify-between items-center gap-3 px-6 pb-6">
                <div class="text-sm text-gray-500">
                    <span v-if="currentImages.length > 0">{{ currentImages.length }} image(s) actuelle(s)</span>
                    <span v-if="pendingImages.length > 0" class="ml-2">• {{ pendingImages.length }} en attente</span>
                </div>

                <div class="flex gap-3">
                    <UButton variant="secondary" :disabled="isUploading" @click="handleClose"> Fermer </UButton>
                    <UButton
                        v-if="pendingImages.length > 0 && accommodationId"
                        :disabled="isUploading"
                        @click="uploadImages"
                    >
                        <svg
                            v-if="!isUploading"
                            class="w-4 h-4 mr-2"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"
                            />
                        </svg>
                        <svg
                            v-else
                            class="w-4 h-4 mr-2 animate-spin"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"
                            />
                        </svg>
                        {{ isUploading ? 'Upload...' : `Ajouter ${pendingImages.length} image(s)` }}
                    </UButton>
                </div>
            </div>
        </div>
    </UBaseModal>
</template>

<script setup lang="ts">
    import { ref, computed, watch, onUnmounted } from 'vue';
    import { useToast } from '~/composables/useToast';
    import { useFileUploadProgress } from '~/composables/useFileUploadProgress';

    interface ImageData {
        url: string;
        file?: File;
        isMain?: boolean;
    }

    interface PendingImage {
        file: File;
        preview: string;
    }

    interface Props {
        isOpen: boolean;
        images?: ImageData[];
        accommodationId?: number;
    }

    interface Emits {
        (e: 'close'): void;
        (e: 'images-updated', images: ImageData[]): void;
    }

    const props = withDefaults(defineProps<Props>(), {
        images: () => [],
        accommodationId: undefined,
    });

    const emit = defineEmits<Emits>();

    const { success, error } = useToast();
    const { $api } = useNuxtApp();
    const { upload, progress, isUploading, isSuccess, responseData } = useFileUploadProgress();

    const fileInput = ref();
    const currentImages = ref<ImageData[]>([...props.images]);
    const pendingImages = ref<PendingImage[]>([]);

    const canAddMore = computed(() => {
        return currentImages.value.length + pendingImages.value.length < 10;
    });

    const handleFileSelect = (files: File | File[]) => {
        const fileArray = Array.isArray(files) ? files : [files];

        for (const file of fileArray) {
            if (!canAddMore.value) {
                error('Limite atteinte', 'Vous pouvez ajouter au maximum 10 images');
                return;
            }

            // Validation
            const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
            if (!allowedTypes.includes(file.type)) {
                error('Format incorrect', 'Utilisez JPEG, PNG ou WebP');
                continue;
            }

            const maxSize = 5 * 1024 * 1024; // 5MB
            if (file.size > maxSize) {
                error('Fichier trop volumineux', 'Taille maximale : 5MB');
                continue;
            }

            // Créer la prévisualisation
            const preview = URL.createObjectURL(file);

            // En mode création, ajouter directement aux images courantes
            if (!props.accommodationId) {
                currentImages.value.push({
                    url: preview,
                    file: file,
                    isMain: currentImages.value.length === 0, // Première image = principale
                });
            } else {
                // En mode édition, ajouter aux images en attente
                pendingImages.value.push({
                    file,
                    preview,
                });
            }
        }

        // Émettre la mise à jour à la fin
        if (!props.accommodationId) {
            emit('images-updated', currentImages.value);
        }
    };

    const removePendingImage = (index: number) => {
        const image = pendingImages.value[index];
        URL.revokeObjectURL(image.preview);
        pendingImages.value.splice(index, 1);
    };

    const removeImage = (index: number) => {
        const image = currentImages.value[index];

        // Nettoyer l'URL d'objet si c'est une prévisualisation
        if (image.url.startsWith('blob:')) {
            URL.revokeObjectURL(image.url);
        }

        currentImages.value.splice(index, 1);

        // Réorganiser les images principales si nécessaire
        if (image.isMain && currentImages.value.length > 0) {
            currentImages.value[0].isMain = true;
        }

        emit('images-updated', currentImages.value);
    };

    const setMainImage = (index: number) => {
        // Réinitialiser toutes les images
        currentImages.value.forEach((img) => (img.isMain = false));
        // Définir la nouvelle image principale
        currentImages.value[index].isMain = true;

        emit('images-updated', currentImages.value);
    };

    const uploadImages = async () => {
        // En mode création, pas besoin d'upload - les images sont déjà dans currentImages
        if (!props.accommodationId) {
            success('Images ajoutées', 'Images ajoutées avec succès');
            return;
        }

        if (pendingImages.value.length === 0) return;

        try {
            // Upload chaque image individuellement
            for (const pendingImage of pendingImages.value) {
                const uploadUrl = $api(`/api/accommodations/${props.accommodationId}/images`);

                // Utiliser upload du composable useFileUploadProgress
                await upload(pendingImage.file, uploadUrl, 'file');

                if (isSuccess.value && responseData.value) {
                    const response = responseData.value as { image?: { url: string; isMain: boolean } };
                    if (response.image) {
                        currentImages.value.push({
                            url: response.image.url,
                            isMain: response.image.isMain || false,
                        });
                    }
                }
            }

            // Nettoyer les images en attente
            pendingImages.value.forEach((img) => URL.revokeObjectURL(img.preview));
            pendingImages.value = [];

            success('Images ajoutées', 'Images ajoutées avec succès');
            emit('images-updated', currentImages.value);
        } catch (err: unknown) {
            error("Erreur d'upload", (err as Error).message || "Erreur lors de l'upload des images");
        }
    };

    const handleClose = () => {
        if (isUploading.value) return;

        // Nettoyer seulement les images en attente (mode édition)
        pendingImages.value.forEach((img) => URL.revokeObjectURL(img.preview));
        pendingImages.value = [];

        emit('close');
    };

    // Sync avec les props uniquement si ce n'est pas vide ou à l'initialisation
    watch(
        () => props.images,
        (newImages) => {
            if (newImages.length > 0 || currentImages.value.length === 0) {
                currentImages.value = [...newImages];
            }
        },
        { deep: true, immediate: true }
    );

    // Nettoyer au démontage
    onUnmounted(() => {
        pendingImages.value.forEach((img) => URL.revokeObjectURL(img.preview));
    });
</script>

<style scoped>
    .group:hover .group-hover\:opacity-100 {
        opacity: 1;
    }
</style>
