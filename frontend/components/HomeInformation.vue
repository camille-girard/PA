<script setup lang="ts">
    import { ref, onMounted, watch, watchEffect, computed } from 'vue';
    import { useRoute, useRouter } from 'vue-router';
    import { nanoid } from 'nanoid';
    import { useAuthStore } from '~/stores/auth';

    const route = useRoute();
    const router = useRouter();
    const config = useRuntimeConfig();
    const auth = useAuthStore();

    const accommodationId = route.params.id;
    const isEditing = computed(() => Boolean(accommodationId));

    const title = ref('');
    const theme = ref('');
    const type = ref('');
    const bedrooms = ref(0);
    const bathrooms = ref(0);
    const capacity = ref(1);
    const description = ref('');
    const practicalInformation = ref('');
    const address = ref('');
    const city = ref('');
    const postalCode = ref('');
    const country = ref('');
    const pricePerNight = ref(0);
    const minStay = ref(1);
    const maxStay = ref(7);
    const latitude = ref<number | null>(null);
    const longitude = ref<number | null>(null);
    const images = ref<{ id: string; url: string }[]>([]);

    const suggestions = ref<any[]>([]);
    const fileInputRef = ref<HTMLInputElement | null>(null);
    const showDeletePopup = ref(false);
    const showSuccessMessage = ref(false);

    const typeOptions = [
        { label: 'Appartement', value: 'Appartement' },
        { label: 'Maison', value: 'Maison' },
        { label: 'Château', value: 'Château' },
    ];

    const themeOptions = ref<{ value: number; label: string }[]>([]);

    const isLoading = ref(true);
    const error = ref(null);
    let debounceTimer: ReturnType<typeof setTimeout> | null = null;

    onMounted(async () => {
        const { $api } = useNuxtApp();
        const rawThemes = await useAuthFetch<any>($api('/api/themes/'));

        themeOptions.value = rawThemes.data.value.themes.map((t: any) => ({
            value: t.id,
            label: t.name,
        }));

        if (!isEditing.value) {
            isLoading.value = false;
            return;
        }

        isLoading.value = true;

        try {
            const res = await fetch(`${config.public.apiUrl}/api/accommodations/${accommodationId}`, {
                credentials: 'include',
            });

            if (!res.ok) throw new Error('Erreur serveur');

            const data = await res.json();

            title.value = data.name;
            description.value = data.description;
            address.value = data.address;
            city.value = data.city;
            postalCode.value = data.postalCode;
            country.value = data.country || 'France';
            theme.value = data.themeId ? Number(data.themeId) : '';
            type.value = data.type;
            practicalInformation.value = data.practicalInformation;
            capacity.value = data.capacity;
            pricePerNight.value = data.price;
            latitude.value = data.latitude;
            longitude.value = data.longitude;
            bedrooms.value = data.bedrooms || 0;
            bathrooms.value = data.bathrooms || 0;

            images.value =
                data.images?.map((img: any) => ({
                    id: nanoid(),
                    url: img.url,
                })) || [];
        } catch (err: any) {
            error.value = err.message;
        } finally {
            isLoading.value = false;
        }
    });

    watchEffect(() => {
        const found = themeOptions.value.find((option) => option.value === theme.value);
        if (!found && typeof theme.value === 'object' && theme.value !== null) {
            theme.value = theme.value.name || '';
        }
    });

    watch(address, (newVal) => {
        if (debounceTimer) clearTimeout(debounceTimer);
        if (!newVal) return;
        debounceTimer = setTimeout(fetchSuggestions, 400);
    });

    async function fetchSuggestions() {
        const query = encodeURIComponent(address.value);
        const accessToken = config.public.mapboxToken;

        const url = `https://api.mapbox.com/geocoding/v5/mapbox.places/${query}.json?access_token=${accessToken}&country=fr&autocomplete=true&limit=5`;

        try {
            const res = await fetch(url);
            const data = await res.json();
            suggestions.value = data.features;
        } catch (e) {
            suggestions.value = [];
        }
    }

    function selectSuggestion(suggestion: any) {
        address.value = suggestion.place_name;
        latitude.value = suggestion.geometry.coordinates[1];
        longitude.value = suggestion.geometry.coordinates[0];
        country.value = 'France';
        suggestions.value = [];
    }

    function handleImageUpload(event: Event) {
        const files = (event.target as HTMLInputElement).files;
        if (!files) return;

        const remainingSlots = 10 - images.value.length;
        const selectedFiles = Array.from(files).slice(0, remainingSlots);

        selectedFiles.forEach((file) => {
            const reader = new FileReader();
            reader.onload = () => {
                images.value.push({
                    id: nanoid(),
                    url: reader.result as string,
                });
            };
            reader.readAsDataURL(file);
        });

        if (fileInputRef.value) {
            fileInputRef.value.value = '';
        }
    }

    function removeImage(id: string) {
        images.value = images.value.filter((img) => img.id !== id);
    }

    async function handleSubmit() {
        const payload: Record<string, any> = {
            name: title.value,
            description: description.value,
            practicalInformation: practicalInformation.value,
            address: address.value,
            city: city.value,
            postalCode: postalCode.value,
            country: country.value,
            type: type.value,
            price: pricePerNight.value,
            capacity: capacity.value,
            bedrooms: bedrooms.value,
            bathrooms: bathrooms.value,
            latitude: latitude.value,
            longitude: longitude.value,
            minStay: minStay.value,
            maxStay: maxStay.value,
            theme: theme.value,
            images: images.value.map((img) => ({
                url: img.url,
            })),
        };

        if (!isEditing.value) {
            payload.ownerId = auth.user?.id;
        }

        const method = isEditing.value ? 'PUT' : 'POST';
        const url = isEditing.value
            ? `${config.public.apiUrl}/api/accommodations/${accommodationId}`
            : `${config.public.apiUrl}/api/accommodations`;

        const res = await fetch(url, {
            method,
            headers: { 'Content-Type': 'application/json' },
            credentials: 'include',
            body: JSON.stringify(payload),
        });

        if (!res.ok) {
            alert('Erreur lors de la sauvegarde');
            return;
        }

        const json = await res.json();

        if (!isEditing.value) {
            router.push(`/accommodations/${json.accommodation.id}/edit`);
        } else {
            showSuccessMessage.value = true;
            setTimeout(() => {
                showSuccessMessage.value = false;
            }, 4000);
        }
    }

    function confirmDelete() {
        showDeletePopup.value = true;
    }

    async function handleConfirmDelete() {
        showDeletePopup.value = false;

        try {
            const res = await fetch(`${config.public.apiUrl}/api/accommodations/${accommodationId}`, {
                method: 'DELETE',
                credentials: 'include',
            });

            if (res.ok) {
                router.push('/my-accommodation');
            } else {
                const errText = await res.text();
                alert('Erreur lors de la suppression : ' + errText);
            }
        } catch (err) {
            alert('Erreur réseau lors de la suppression');
            console.error(err);
        }
    }
</script>

<template>
    <div
        v-if="showSuccessMessage"
        class="fixed top-5 left-1/2 transform -translate-x-1/2 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50"
    >
        Hébergement {{ isEditing ? 'modifié' : 'créé' }} avec succès !
    </div>

    <div v-if="showDeletePopup" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
        <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-sm">
            <h3 class="text-lg font-semibold mb-4">Confirmer la suppression</h3>
            <p class="mb-6">Êtes-vous sûr de vouloir supprimer ce logement ?</p>
            <div class="flex justify-end gap-4">
                <UButton variant="secondary" @click="showDeletePopup = false">Annuler</UButton>
                <UButton variant="danger" @click="handleConfirmDelete">Supprimer</UButton>
            </div>
        </div>
    </div>

    <form class="space-y-16 max-w-4xl mx-auto" @submit.prevent="handleSubmit">
        <section>
            <h2 class="text-h2 font-bold mb-6">Informations du logement</h2>
            <UInput v-model="title" label="Titre de l'annonce" placeholder="Titre de l'annonce" required />

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
                <USelectBox
                    v-model="theme"
                    :options="themeOptions"
                    label="Thème"
                    placeholder="Sélectionner..."
                    required
                />
                <USelectBox
                    v-model="type"
                    :options="typeOptions"
                    label="Type de location"
                    placeholder="Sélectionner..."
                    required
                />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                <UInputNumber v-model="bedrooms" label="Chambres" :min="0" />
                <UInputNumber v-model="bathrooms" label="Salles de bain" :min="0" />
                <UInputNumber v-model="capacity" label="Capacité" :min="1" required />
            </div>

            <div class="mt-4">
                <label for="description" class="block mb-2 text-sm font-medium text-gray-700">Description *</label>
                <textarea
                    id="description"
                    v-model="description"
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
                    v-model="practicalInformation"
                    rows="4"
                    maxlength="20000"
                    placeholder="Informations pratiques pour les voyageurs"
                    class="w-full rounded-lg border border-gray-300 focus:ring-orange-500 focus:border-orange-500 px-4 py-3"
                />
                <p class="text-sm text-gray-400 mt-1">Séparez par des virgules ex: Wi-Fi gratuit, cuisine équipée</p>
            </div>
        </section>
        <section>
            <h2 class="text-h2 mb-6">Adresse</h2>
            <UInput v-model="address" label="Adresse complète" placeholder="Adresse complète" required />
            <ul v-if="suggestions.length" class="border mt-2 rounded-md bg-white shadow relative z-10">
                <li
                    v-for="s in suggestions"
                    :key="s.id"
                    class="px-4 py-2 hover:bg-gray-100 cursor-pointer"
                    @click="selectSuggestion(s)"
                >
                    {{ s.place_name }}
                </li>
            </ul>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                <UInput v-model="city" label="Ville" placeholder="Ville" required />
                <UInput v-model="postalCode" label="Code postal" placeholder="Code postal" required />
            </div>
            <UInput v-model="country" label="Pays" placeholder="Pays" class="mt-4" required />
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

            <div v-if="images.length" class="grid grid-cols-2 md:grid-cols-3 gap-4 mt-6">
                <div v-for="img in images" :key="img.id" class="relative group">
                    <img :src="img.url" class="w-full h-40 object-cover rounded-xl" />
                    <button
                        type="button"
                        class="absolute top-2 right-2 bg-black/60 text-white w-6 h-6 flex items-center justify-center rounded-full text-sm opacity-0 group-hover:opacity-100 transition"
                        @click="removeImage(img.id)"
                    >
                        ×
                    </button>
                </div>
            </div>
        </section>
        <section>
            <h2 class="text-h2 mb-6">Prix et disponibilité</h2>
            <UInputNumber v-model="pricePerNight" label="Prix par nuit" :min="1" required />
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                <UInputNumber v-model="minStay" label="Séjour minimum" :min="1" />
                <UInputNumber v-model="maxStay" label="Séjour maximum" :min="1" />
            </div>
        </section>
        <div class="flex justify-between pt-10 gap-6">
            <UButton type="submit" variant="primary" class="w-full max-w-md">Enregistrer et continuer</UButton>
            <UButton v-if="isEditing" type="button" variant="danger" class="w-full max-w-md" @click="confirmDelete">
                Supprimer
            </UButton>
        </div>
    </form>
</template>
