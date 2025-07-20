import { ref, computed } from 'vue';
import { nanoid } from 'nanoid';
import { useRouter } from 'vue-router';
import type { AccommodationImage } from '~/types/accommodationImage';
import type { Theme } from '~/types/theme';
import { useToast } from '~/composables/useToast';

export interface FormState {
    title: string;
    theme: number | string;
    type: string;
    bedrooms: number;
    bathrooms: number;
    capacity: number;
    description: string;
    practicalInformation: string;
    advantages: string[];
    address: string;
    city: string;
    postalCode: string;
    country: string;
    pricePerNight: number;
    minStay: number;
    maxStay: number;
    latitude: number | null;
    longitude: number | null;
    images: { id: string; url: string; file?: File; isMain?: boolean }[];
}

interface UseAccommodationFormOptions {
    accommodationId?: string | string[];
}

export function useAccommodationForm(options: UseAccommodationFormOptions = {}) {
    const router = useRouter();

    // Form state
    const formState = ref<FormState>({
        title: '',
        theme: '',
        type: '',
        bedrooms: 0,
        bathrooms: 0,
        capacity: 1,
        description: '',
        practicalInformation: '',
        advantages: [],
        address: '',
        city: '',
        postalCode: '',
        country: 'France',
        pricePerNight: 0,
        minStay: 1,
        maxStay: 7,
        latitude: null,
        longitude: null,
        images: [],
    });

    const isEditing = computed(() => Boolean(options.accommodationId));
    const isLoading = ref(true);
    const error = ref<string | null>(null);

    // Theme options
    const themeOptions = ref<{ value: number; label: string }[]>([]);

    // Get accommodation data if editing
    async function fetchAccommodationData() {
        if (!isEditing.value) {
            isLoading.value = false;
            return;
        }

        isLoading.value = true;

        try {
            const { $api } = useNuxtApp();
            const { data: response, error } = await useAuthFetch($api(`/api/accommodations/${options.accommodationId}`));

            if (error.value) throw new Error(error.value.message || 'Erreur serveur');

            const data = response.value;

            let advantages = [];
            if (data.advantage && Array.isArray(data.advantage)) {
                const labelToValueMap: { [key: string]: string } = {
                    'Petit-déjeuner offert': 'breakfast',
                    Goûter: 'snack',
                    Jardin: 'garden',
                    'Salle de jeux': 'game_room',
                    Terrasse: 'terrace',
                    Balcon: 'balcony',
                    Barbecue: 'barbecue',
                    'Accès au château': 'castle_access',
                    'Visite du château': 'castle_visit',
                    'Wi-Fi gratuit': 'wifi',
                    Parking: 'parking',
                    Piscine: 'pool',
                    Spa: 'spa',
                    'Salle de sport': 'gym',
                    'Cuisine équipée': 'kitchen',
                    'Lave-linge': 'laundry',
                    Climatisation: 'air_conditioning',
                    Chauffage: 'heating',
                    Télévision: 'tv',
                    'Accès à la salle commune': 'common_room_access',
                };

                advantages = data.advantage.map((adv: string | { value: string }) => {
                    if (typeof adv === 'object' && adv.value) {
                        return adv.value;
                    }
                    if (typeof adv === 'string') {
                        return labelToValueMap[adv] || adv;
                    }
                    return adv;
                });
            }

            console.log('Processed advantages:', advantages);

            formState.value = {
                title: data.name,
                description: data.description,
                address: data.address,
                city: data.city || '',
                postalCode: data.postalCode || '',
                country: data.country || 'France',
                theme: data.themeId ? Number(data.themeId) : '',
                type: data.type || '',
                practicalInformation: data.practicalInformation || '',
                capacity: data.capacity || 1,
                pricePerNight: data.price || 0,
                latitude: data.latitude || null,
                longitude: data.longitude || null,
                bedrooms: data.bedrooms || 0,
                bathrooms: data.bathrooms || 0,
                minStay: data.minStay || 1,
                maxStay: data.maxStay || 7,
                images:
                    data.images?.map((img: AccommodationImage) => ({
                        id: nanoid(),
                        url: img.url,
                    })) || [],
                advantages,
            };
        } catch (err) {
            const toast = useToast();
            const errorMessage = err instanceof Error ? err.message : 'Erreur inconnue';
            error.value = errorMessage;
            toast.error('Erreur de chargement', errorMessage);
        } finally {
            isLoading.value = false;
        }
    }

    // Load theme options
    async function fetchThemes() {
        const { $api } = useNuxtApp();
        try {
            const rawThemes = await useAuthFetch<{ themes: Theme[] }>($api('/api/themes'));

            if (rawThemes.data.value) {
                themeOptions.value = rawThemes.data.value.themes.map((t) => ({
                    value: t.id,
                    label: t.name || t.description || 'Thème sans nom',
                }));
            }
        } catch (err) {
            const toast = useToast();
            console.error('Error fetching themes:', err);
            toast.error('Erreur de chargement', 'Impossible de charger les thèmes');
        }
    }

    // Handle form submission
    async function handleSubmit() {
        const { $api } = useNuxtApp();
        const method = isEditing.value ? 'PUT' : 'POST';
        const endpoint = isEditing.value
            ? `/api/accommodations/${options.accommodationId}`
            : `/api/accommodations`;

        try {
            // Créer un FormData pour envoyer les données et les fichiers
            const formData = new FormData();
            
            // Ajouter les données du formulaire
            formData.append('name', formState.value.title);
            formData.append('description', formState.value.description);
            formData.append('practicalInformation', formState.value.practicalInformation || '');
            formData.append('address', formState.value.address);
            formData.append('city', formState.value.city || '');
            formData.append('postalCode', formState.value.postalCode || '');
            formData.append('country', formState.value.country || 'France');
            formData.append('type', formState.value.type || '');
            formData.append('price', formState.value.pricePerNight.toString());
            formData.append('capacity', formState.value.capacity.toString());
            formData.append('bedrooms', formState.value.bedrooms.toString());
            formData.append('bathrooms', formState.value.bathrooms.toString());
            formData.append('latitude', formState.value.latitude?.toString() || '');
            formData.append('longitude', formState.value.longitude?.toString() || '');
            formData.append('minStay', formState.value.minStay.toString());
            formData.append('maxStay', formState.value.maxStay.toString());
            formData.append('theme', formState.value.theme?.toString() || '');
            
            // Ajouter les fichiers images
            formState.value.images.forEach((image, index) => {
                if (image.file) {
                    formData.append(`image_${index}`, image.file);
                }
            });

            interface AccommodationResponse {
                message: string;
                accommodation: {
                    id: number;
                };
            }

            const { error } = await useAuthFetch<AccommodationResponse>($api(endpoint), {
                method,
                body: formData,
            });

            if (error.value) {
                throw new Error(error.value.message || 'Erreur lors de la sauvegarde');
            }

            if (!isEditing.value) {
                router.push(`/my-accommodation`);
            } else {
                const toast = useToast();
                toast.success(
                    'Sauvegarde réussie',
                    `Votre hébergement "${formState.value.title}" a été mis à jour avec succès.`
                );
            }
        } catch (err) {
            console.error('Submit error:', err);
            const toast = useToast();
            toast.error(
                'Erreur de sauvegarde',
                err instanceof Error
                    ? err.message
                    : 'Une erreur est survenue lors de la sauvegarde de votre hébergement.'
            );
        }

        console.log('=== FORM SUBMISSION END ===');
    }

    // Handle accommodation deletion
    async function deleteAccommodation() {
        try {
            const { $api } = useNuxtApp();
            const { error } = await useAuthFetch($api(`/api/accommodations/${options.accommodationId}`), {
                method: 'DELETE',
            });

            if (error.value) {
                const toast = useToast();
                toast.error('Erreur de suppression', `Impossible de supprimer l'hébergement : ${error.value.message || 'Erreur serveur'}`);
            } else {
                const toast = useToast();
                toast.success('Suppression réussie', 'Votre hébergement a été supprimé avec succès.');
                router.push('/my-accommodation');
            }
        } catch (err) {
            const toast = useToast();
            toast.error('Erreur réseau', 'Erreur de connexion lors de la tentative de suppression.');
            console.error(err);
        }
    }

    // Handle image operations
    function addImages(files: File[]) {
        const remainingSlots = 10 - formState.value.images.length;
        const selectedFiles = files.slice(0, remainingSlots);

        selectedFiles.forEach((file) => {
            const reader = new FileReader();
            reader.onload = () => {
                formState.value.images.push({
                    id: nanoid(),
                    url: reader.result as string,
                    file: file,
                    isMain: formState.value.images.length === 0, // Première image = principale
                });
            };
            reader.readAsDataURL(file);
        });
    }

    function removeImage(id: string) {
        formState.value.images = formState.value.images.filter((img) => img.id !== id);
    }

    return {
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
    };
}
