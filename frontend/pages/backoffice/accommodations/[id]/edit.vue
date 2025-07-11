<script setup lang="ts">
    import Input from '~/components/atoms/UInput.vue';
    import UInputNumber from '~/components/atoms/UInputNumber.vue';
    import Textarea from '~/components/atoms/UTextarea.vue';
    import UButton from '~/components/atoms/UButton.vue';
    import { useRuntimeConfig } from '#app';
    import { useAuthFetch } from '~/composables/useAuthFetch';

    interface Theme {
        id: number;
        name: string;
    }

    interface Accommodation {
        id: number;
        name: string;
        description?: string;
        address: string;
        city?: string;
        postalCode?: string;
        country?: string;
        type?: string;
        price: number;
        capacity: number;
        bedrooms?: number;
        bathrooms?: number;
        advantage: string[];
        practicalInformations?: string;
        latitude?: number;
        longitude?: number;
        themeId?: number;
    }

    definePageMeta({
        layout: 'backoffice',
        middleware: 'admin',
    });

    const route = useRoute();
    const id = route.params.id as string;
    const {
        public: { apiUrl },
    } = useRuntimeConfig();

    const pending = ref(false);
    const saving = ref(false);
    const success = ref(false);
    const errorMsg = ref('');

    const themes = ref<Theme[]>([]);
    const selectedThemeId = ref<number | null>(null);

    const accommodation = ref<Accommodation | null>(null);

    const form = reactive({
        name: '',
        description: '',
        address: '',
        city: '',
        postalCode: '',
        country: '',
        type: '',
        price: 0,
        capacity: 1,
        bedrooms: 0,
        bathrooms: 0,
        practicalInformations: '',
        advantage: '',
        latitude: '',
        longitude: '',
    });

    async function loadThemes() {
        try {
            const { data, error } = await useAuthFetch<{ themes: Theme[] }>('/api/themes', {
                baseURL: apiUrl,
            });
            if (error.value) {
                throw error.value;
            }
            themes.value = data.value?.themes ?? [];
        } catch (err: unknown) {
            console.error('Erreur chargement des thèmes:', err);
        }
    }

    const themeList = computed(() => themes.value);

    async function loadAccommodation() {
        pending.value = true;
        errorMsg.value = '';
        try {
            const { data, error } = await useAuthFetch<Accommodation>(`/api/accommodations/${id}`, {
                baseURL: apiUrl,
            });
            if (error.value) {
                throw error.value;
            }

            accommodation.value = data.value ?? null;
            if (accommodation.value) {
                const acc = accommodation.value;
                form.name = acc.name;
                form.description = acc.description ?? '';
                form.address = acc.address;
                form.city = acc.city ?? '';
                form.postalCode = acc.postalCode ?? '';
                form.country = acc.country ?? '';
                form.type = acc.type ?? '';
                form.price = acc.price;
                form.capacity = acc.capacity;
                form.bedrooms = acc.bedrooms ?? 0;
                form.bathrooms = acc.bathrooms ?? 0;
                form.practicalInformations = acc.practicalInformations ?? '';
                form.advantage = acc.advantage?.join('\n') ?? '';
                form.latitude = acc.latitude?.toString() ?? '';
                form.longitude = acc.longitude?.toString() ?? '';
                selectedThemeId.value = acc.themeId ?? null;
            }
        } catch (err: unknown) {
            if (
                typeof err === 'object' &&
                err &&
                'data' in err &&
                (err as { data?: { message?: string } }).data?.message
            ) {
                errorMsg.value = (err as { data?: { message?: string } }).data!.message!;
            } else {
                errorMsg.value = 'Erreur lors du chargement.';
            }
            console.error(err);
        } finally {
            pending.value = false;
        }
    }

    async function refresh() {
        await loadAccommodation();
    }

    async function save() {
        saving.value = true;
        success.value = false;
        errorMsg.value = '';
        try {
            await useAuthFetch(`/api/accommodations/${id}`, {
                method: 'PUT',
                baseURL: apiUrl,
                body: {
                    name: form.name,
                    description: form.description,
                    address: form.address,
                    city: form.city,
                    postalCode: form.postalCode,
                    country: form.country,
                    type: form.type,
                    price: form.price,
                    capacity: form.capacity,
                    bedrooms: form.bedrooms,
                    bathrooms: form.bathrooms,
                    practicalInformations: form.practicalInformations,
                    advantage: form.advantage
                        .split('\n')
                        .map((a) => a.trim())
                        .filter(Boolean),
                    latitude: form.latitude ? parseFloat(form.latitude) : null,
                    longitude: form.longitude ? parseFloat(form.longitude) : null,
                    themeId: selectedThemeId.value,
                },
            });

            success.value = true;
            await refresh();
        } catch (error: unknown) {
            errorMsg.value = error?.data?.message || 'Erreur lors de l’enregistrement.';
        } finally {
            saving.value = false;
        }
    }

    onMounted(() => {
        loadThemes();
        loadAccommodation();
    });
</script>

<template>
    <div class="space-y-6">
      <ULink to="/backoffice/accommodations" size="lg" class="flex flex-row gap-2" >
        <ArrowLeftIcon /> Retour à la liste
      </ULink>
        <h1 class="text-2xl font-semibold">Modifier le logement</h1>

        <form :aria-busy="saving || pending" class="flex flex-col gap-6" @submit.prevent="save">
            <div class="flex flex-wrap gap-4">
                <Input v-model="form.name" label="Nom" type="text" class="flex-1 min-w-[250px]" required />
                <Input v-model="form.type" label="Type" type="text" class="flex-1 min-w-[250px]" />
            </div>

            <div class="flex flex-wrap gap-4">
                <Input v-model="form.address" label="Adresse" type="text" class="flex-1 min-w-[250px]" required />
                <Input v-model="form.city" label="Ville" type="text" class="flex-1 min-w-[250px]" />
                <Input v-model="form.postalCode" label="Code Postal" type="text" class="flex-1 min-w-[250px]" />
                <Input v-model="form.country" label="Pays" type="text" class="flex-1 min-w-[250px]" />
            </div>

            <div class="flex flex-wrap gap-4">
                <UInputNumber
                    v-model="form.price"
                    label="Prix / nuit (€)"
                    :step="0.01"
                    :min="0"
                    required
                    class="flex-1 min-w-[200px]"
                />
                <UInputNumber v-model="form.capacity" label="Capacité" :min="1" required class="flex-1 min-w-[200px]" />
                <UInputNumber v-model="form.bedrooms" label="Chambres" :min="0" class="flex-1 min-w-[200px]" />
                <UInputNumber v-model="form.bathrooms" label="Salles de bain" :min="0" class="flex-1 min-w-[200px]" />
            </div>

            <div class="flex flex-wrap gap-4">
                <Input v-model="form.latitude" label="Latitude" type="number" step="any" class="flex-1 min-w-[200px]" />
                <Input
                    v-model="form.longitude"
                    label="Longitude"
                    type="number"
                    step="any"
                    class="flex-1 min-w-[200px]"
                />
            </div>

            <div class="flex flex-col gap-1.5">
                <label class="text-body-sm"> Thème <span class="text-brand-tertiary">*</span> </label>
                <select
                    v-model="selectedThemeId"
                    required
                    class="border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:outline-none shadow-xs w-full px-3 py-2 text-sm"
                >
                    <option disabled>Choisir un thème</option>
                    <option v-for="theme in themeList" :key="theme.id" :value="theme.id">
                        {{ theme.name }}
                    </option>
                </select>
            </div>

            <Textarea v-model="form.description" label="Description" />
            <Textarea v-model="form.practicalInformations" label="Informations pratiques" />
            <Textarea
                v-model="form.advantage"
                label="Avantages (un par ligne)"
                placeholder="Exemple :&#10;Wi-Fi gratuit&#10;Petit-déjeuner offert"
            />

            <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                <UButton :disabled="saving" :is-loading="saving" size="lg" variant="primary" type="submit">
                    {{ saving ? 'Enregistrement…' : 'Enregistrer' }}
                </UButton>
                <span v-if="success" class="text-green-600 text-sm">Modifications enregistrées</span>
                <span v-if="errorMsg" class="text-red-600 text-sm">{{ errorMsg }}</span>
            </div>
        </form>
    </div>
</template>
