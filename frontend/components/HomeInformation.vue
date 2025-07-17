<script setup lang="ts">
    /**
     * Composant de formulaire pour créer ou modifier un hébergement.
     * Ce composant utilise les composables useAccommodationForm et useAddressSuggestions
     * pour gérer la logique métier et l'interaction avec l'API.
     */
    import { ACCOMMODATION_TYPES } from '~/constants/accommodationTypes';
    import { ACCOMMODATION_ADVANTAGES } from '~/constants/accommodationAdvantages';
    import type { MapSuggestion } from '~/composables/useAddressSuggestions';
    import { useToast } from '~/composables/useToast';
    import UBaseModal from '~/components/molecules/UBaseModal.vue';

    const route = useRoute();
    const accommodationId = route.params.id;

    const fileInputRef = ref<HTMLInputElement | null>(null);
    const isDeleteModalOpen = ref(false);

    const {
        formState,
        isEditing,
        isLoading,
        error,
        themeOptions,
        fetchAccommodationData,
        fetchThemes,
        handleSubmit,
        deleteAccommodation,
        addImages,
        removeImage,
    } = useAccommodationForm({ accommodationId });

    const { suggestions, fetchSuggestions, selectSuggestion } = useAddressSuggestions();

    /**
     * Initialisation des données au montage du composant
     */
    const toast = useToast();

    onMounted(async () => {
        try {
            await Promise.all([fetchThemes(), fetchAccommodationData()]);
        } catch (err) {
            console.error('Erreur lors du chargement des données:', err);
            toast.error('Erreur de chargement', "Impossible de charger les données de l'hébergement.");
        }
    });

    /**
     * Gestion des thèmes pour résoudre les cas où le thème est un objet au lieu d'un ID
     */
    watchEffect(() => {
        const found = themeOptions.value.find((option) => option.value === formState.value.theme);
        if (!found && typeof formState.value.theme === 'object' && formState.value.theme !== null) {
            // @ts-expect-error - Gestion du cas où le thème est un objet au lieu d'un ID
            formState.value.theme = formState.value.theme.name || '';
        }
    });

    /**
     * Observer les changements d'adresse pour mettre à jour les suggestions
     */
    watch(
        () => formState.value.address,
        async (newVal) => {
            if (!newVal) return;
            await fetchSuggestions(newVal);
        }
    );

    /**
     * Sélectionne une suggestion d'adresse et met à jour le formulaire
     */
    function handleSelectSuggestion(suggestion: MapSuggestion): void {
        const { address, latitude, longitude } = selectSuggestion(suggestion);
        formState.value.address = address;
        formState.value.latitude = latitude;
        formState.value.longitude = longitude;
        formState.value.country = 'France';

        toast.info('Adresse sélectionnée', 'Les coordonnées géographiques ont été mises à jour.');
    }

    /**
     * Gestion des avantages sélectionnés
     */
    /*
    function toggleAdvantage(value: string) {
        const advantages = formState.value.advantages;
        const index = advantages.indexOf(value);

        if (index > -1) {
            advantages.splice(index, 1);
        } else {
            advantages.push(value);
        }
    }
     */

    function isAdvantageSelected(value: string): boolean {
        return formState.value.advantages.includes(value);
    }

    /**
     * Gère le changement d'état d'une checkbox d'avantage
     */
    function handleAdvantageChange(value: string, checked: boolean) {
        console.log('handleAdvantageChange called:', { value, checked });
        const advantages = formState.value.advantages;
        const index = advantages.indexOf(value);

        console.log('Current advantages before change:', advantages);

        if (checked && index === -1) {
            advantages.push(value);
            console.log('Added advantage:', value);
        } else if (!checked && index > -1) {
            advantages.splice(index, 1);
            console.log('Removed advantage:', value);
        }

        console.log('Current advantages after change:', formState.value.advantages);
        console.log('Total advantages count:', formState.value.advantages.length);
    }

    /**
     * Gère l'upload d'images et met à jour le formulaire
     */
    function handleImageUpload(event: Event): void {
        const files = (event.target as HTMLInputElement).files;
        if (!files) return;

        const fileArray = Array.from(files);
        addImages(fileArray);

        // Reset l'input file pour permettre de sélectionner à nouveau les mêmes fichiers
        if (fileInputRef.value) {
            fileInputRef.value.value = '';
        }

        toast.success(
            'Images ajoutées',
            `${fileArray.length} image${fileArray.length > 1 ? 's' : ''} ajoutée${fileArray.length > 1 ? 's' : ''} avec succès.`
        );
    }

    /**
     * Affiche la popup de confirmation de suppression
     */
    function confirmDelete(): void {
        isDeleteModalOpen.value = true;
    }

    /**
     * Supprime l'hébergement après confirmation
     */
    function handleConfirmDelete(): void {
        isDeleteModalOpen.value = false;
        deleteAccommodation();
    }

    /**
     * Ferme la modal de suppression
     */
    function closeDeleteModal(): void {
        isDeleteModalOpen.value = false;
    }
</script>

<template>
    <UBaseModal :is-open="isDeleteModalOpen" @close="closeDeleteModal">
        <div class="p-6">
            <h3 class="text-lg font-semibold mb-4">Confirmer la suppression</h3>
            <p class="mb-6">Êtes-vous sûr de vouloir supprimer ce logement ?</p>
            <div class="flex justify-end gap-4">
                <UButton variant="secondary" @click="closeDeleteModal">Annuler</UButton>
                <UButton color="red" variant="primary" @click="handleConfirmDelete">Supprimer</UButton>
            </div>
        </div>
    </UBaseModal>

    <div v-if="error" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 max-w-4xl mx-auto">
        <strong class="font-bold">Erreur:</strong>
        <span class="block sm:inline">{{ error }}</span>
    </div>

    <div v-if="isLoading" class="flex justify-center items-center py-12">
        <ULoading />
        <span class="ml-2">Chargement...</span>
    </div>

    <form v-else class="space-y-16 max-w-4xl mx-auto" @submit.prevent="handleSubmit">
        <section>
            <h2 class="text-h2 font-bold mb-6">Informations du logement</h2>
            <UInput
                v-model="formState.title"
                label="Titre de l'annonce"
                placeholder="Titre de l'annonce"
                type="text"
                required
            />

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
                <USelectBox
                    v-model="formState.theme"
                    :options="themeOptions"
                    label="Thème"
                    placeholder="Sélectionner..."
                    required
                />
                <USelectBox
                    v-model="formState.type"
                    :options="ACCOMMODATION_TYPES"
                    label="Type de location"
                    placeholder="Sélectionner..."
                    required
                />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                <UInputNumber v-model="formState.bedrooms" label="Chambres" :min="0" />
                <UInputNumber v-model="formState.bathrooms" label="Salles de bain" :min="0" />
                <UInputNumber v-model="formState.capacity" label="Capacité" :min="1" required />
            </div>

            <div class="mt-4">
                <label for="description" class="block mb-2 text-sm font-medium text-gray-700">Description *</label>
                <textarea
                    id="description"
                    v-model="formState.description"
                    rows="6"
                    maxlength="20000"
                    placeholder="Décrivez votre logement"
                    class="w-full rounded-lg border border-gray-300 focus:ring-orange-500 focus:border-orange-500 px-4 py-3"
                />
                <p class="text-sm text-gray-400 mt-1">Maximum 20 000 caractères.</p>
            </div>

            <div class="mt-4">
                <label for="practicalInformation" class="block mb-2 text-sm font-medium text-gray-700">
                    Informations pratiques
                </label>
                <textarea
                    id="practicalInformation"
                    v-model="formState.practicalInformation"
                    rows="4"
                    maxlength="1000"
                    placeholder="Instructions d'arrivée, codes d'accès, contacts d'urgence..."
                    class="w-full rounded-lg border border-gray-300 focus:ring-orange-500 focus:border-orange-500 px-4 py-3"
                />
                <p class="text-sm text-gray-400 mt-1">Maximum 1000 caractères.</p>
            </div>

            <div class="mt-6">
                <label class="block mb-4 text-sm font-medium text-gray-700">Avantages de votre logement</label>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                    <UCheckbox
                        v-for="advantage in ACCOMMODATION_ADVANTAGES"
                        :key="advantage.value"
                        :model-value="isAdvantageSelected(advantage.value)"
                        :name="advantage.value"
                        :label="advantage.label"
                        @update:model-value="(checked) => handleAdvantageChange(advantage.value, checked)"
                    />
                </div>
            </div>
        </section>

        <section>
            <h2 class="text-h2 mb-6">Adresse</h2>
            <UInput
                v-model="formState.address"
                label="Adresse complète"
                placeholder="Adresse complète"
                type="text"
                required
            />
            <ul v-if="suggestions.length" class="border mt-2 rounded-md bg-white shadow relative">
                <li
                    v-for="s in suggestions"
                    :key="s.id"
                    class="px-4 py-2 hover:bg-gray-100 cursor-pointer"
                    @click="handleSelectSuggestion(s)"
                >
                    {{ s.place_name }}
                </li>
            </ul>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                <UInput v-model="formState.city" label="Ville" placeholder="Ville" type="text" required />
                <UInput
                    v-model="formState.postalCode"
                    label="Code postal"
                    placeholder="Code postal"
                    type="text"
                    required
                />
            </div>
            <UInput v-model="formState.country" label="Pays" placeholder="Pays" type="text" class="mt-4" required />
        </section>

        <section>
            <h2 class="text-h2 mb-6">Ajouter vos photos</h2>
            <input
                ref="fileInputRef"
                type="file"
                class="hidden"
                accept="image/*"
                multiple
                @change="handleImageUpload"
            />

            <div
                class="w-full h-52 rounded-2xl bg-gray-100 flex flex-col items-center justify-center cursor-pointer hover:bg-gray-200 border-dashed border-2"
                @click="fileInputRef?.click()"
                @dragover.prevent
                @drop.prevent
            >
                <img src="/icon.svg" alt="Ajouter" class="w-10 h-10 opacity-40" />
                <p class="mt-2 text-sm text-gray-400 text-center">Cliquez ou glissez jusqu'à 10 images ici</p>
            </div>

            <div v-if="formState.images.length" class="grid grid-cols-2 md:grid-cols-3 gap-4 mt-6">
                <div v-for="img in formState.images" :key="img.id" class="relative group">
                    <img :src="img.url" class="w-full h-40 object-cover rounded-xl" />
                    <button
                        type="button"
                        class="absolute top-2 right-2 bg-black/60 text-white w-6 h-6 flex items-center justify-center rounded-full text-sm opacity-0 group-hover:opacity-100 transition"
                        @click="
                            () => {
                                removeImage(img.id);
                                toast.info('Image supprimée', 'L\'image a été supprimée de votre hébergement.');
                            }
                        "
                    >
                        ×
                    </button>
                </div>
            </div>
        </section>

        <section>
            <h2 class="text-h2 mb-6">Prix et disponibilité</h2>
            <UInputNumber v-model="formState.pricePerNight" label="Prix par nuit" :min="1" required />
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                <UInputNumber v-model="formState.minStay" label="Séjour minimum" :min="1" />
                <UInputNumber v-model="formState.maxStay" label="Séjour maximum" :min="1" />
            </div>
        </section>

        <div class="flex justify-between pt-10 gap-6">
            <UButton type="submit" variant="primary" class="w-full"> Enregistrer et continuer </UButton>
            <UButton v-if="isEditing" type="button" variant="primary" color="red" class="w-full" @click="confirmDelete">
                Supprimer
            </UButton>
        </div>
    </form>
</template>
