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
  address: string;
  city: string;
  postalCode: string;
  country: string;
  pricePerNight: number;
  minStay: number;
  maxStay: number;
  latitude: number | null;
  longitude: number | null;
  images: { id: string; url: string }[];
}

interface UseAccommodationFormOptions {
  accommodationId?: string | string[];
}

export function useAccommodationForm(options: UseAccommodationFormOptions = {}) {
  const router = useRouter();
  const config = useRuntimeConfig();
  const auth = useAuthStore();
  
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
    address: '',
    city: '',
    postalCode: '',
    country: 'France',
    pricePerNight: 0,
    minStay: 1,
    maxStay: 7,
    latitude: null,
    longitude: null,
    images: []
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
      const res = await fetch(`${config.public.apiUrl}/api/accommodations/${options.accommodationId}`, {
        credentials: 'include',
      });
      
      if (!res.ok) throw new Error('Erreur serveur');
      
      const data = await res.json();
      
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
        images: data.images?.map((img: AccommodationImage) => ({
          id: nanoid(),
          url: img.url,
        })) || [],
      };
    } catch (err) {
      error.value = err instanceof Error ? err.message : 'Erreur inconnue';
    } finally {
      isLoading.value = false;
    }
  }

  // Load theme options
  async function fetchThemes() {
    const { $api } = useNuxtApp();
    try {
      const rawThemes = await useAuthFetch<{ themes: Theme[] }>($api('/api/themes/'));
      
      if (rawThemes.data.value) {
        themeOptions.value = rawThemes.data.value.themes.map((t) => ({
          value: t.id,
          label: t.title || t.description || 'Thème sans nom',
        }));
      }
    } catch (err) {
      console.error('Error fetching themes:', err);
    }
  }

  // Handle form submission
  async function handleSubmit() {
    const payload = {
      name: formState.value.title,
      description: formState.value.description,
      practicalInformation: formState.value.practicalInformation,
      address: formState.value.address,
      city: formState.value.city,
      postalCode: formState.value.postalCode,
      country: formState.value.country,
      type: formState.value.type,
      price: formState.value.pricePerNight,
      capacity: formState.value.capacity,
      bedrooms: formState.value.bedrooms,
      bathrooms: formState.value.bathrooms,
      latitude: formState.value.latitude,
      longitude: formState.value.longitude,
      minStay: formState.value.minStay,
      maxStay: formState.value.maxStay,
      theme: formState.value.theme,
      images: formState.value.images.map(img => ({
        url: img.url,
      })),
    };
    
    if (!isEditing.value && auth.user?.id) {
      // eslint-disable-next-line @typescript-eslint/ban-ts-comment
      // @ts-ignore
      payload.ownerId = auth.user.id;
    }
    
    const method = isEditing.value ? 'PUT' : 'POST';
    const url = isEditing.value
      ? `${config.public.apiUrl}/api/accommodations/${options.accommodationId}`
      : `${config.public.apiUrl}/api/accommodations`;
      
    try {
      const res = await fetch(url, {
        method,
        headers: { 'Content-Type': 'application/json' },
        credentials: 'include',
        body: JSON.stringify(payload),
      });
      
      if (!res.ok) {
        throw new Error('Erreur lors de la sauvegarde');
      }
      
      const json = await res.json();
      
      if (!isEditing.value) {
        router.push(`/accommodations/${json.accommodation.id}/edit`);
      } else {
        const toast = useToast();
        toast.success(
          'Sauvegarde réussie', 
          `Votre hébergement "${formState.value.title}" a été mis à jour avec succès.`
        );
      }
    } catch (err) {
      const toast = useToast();
      toast.error(
        'Erreur de sauvegarde', 
        err instanceof Error ? err.message : 'Une erreur est survenue lors de la sauvegarde de votre hébergement.'
      );
    }
  }

  // Handle accommodation deletion
  async function deleteAccommodation() {
    try {
      const res = await fetch(`${config.public.apiUrl}/api/accommodations/${options.accommodationId}`, {
        method: 'DELETE',
        credentials: 'include',
      });
      
      if (res.ok) {
        const toast = useToast();
        toast.success(
          'Suppression réussie', 
          'Votre hébergement a été supprimé avec succès.'
        );
        router.push('/my-accommodation');
      } else {
        const errText = await res.text();
        const toast = useToast();
        toast.error(
          'Erreur de suppression',
          `Impossible de supprimer l'hébergement : ${errText}`
        );
      }
    } catch (err) {
      const toast = useToast();
      toast.error(
        'Erreur réseau',
        'Erreur de connexion lors de la tentative de suppression.'
      );
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
        });
      };
      reader.readAsDataURL(file);
    });
  }
  
  function removeImage(id: string) {
    formState.value.images = formState.value.images.filter(img => img.id !== id);
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
