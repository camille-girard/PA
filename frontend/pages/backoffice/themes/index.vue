<script setup lang="ts">
    import { ref, reactive, onMounted } from 'vue';
    import { useRuntimeConfig } from '#app';
    import { useAuthFetch } from '~/composables/useAuthFetch';
    import { useToast } from '~/composables/useToast';
    import UCard from '~/components/molecules/UCard.vue';
    import UInput from '~/components/atoms/UInput.vue';
    import UTextarea from '~/components/atoms/UTextarea.vue';
    import UButton from '~/components/atoms/UButton.vue';
    import ConfirmPopover from '~/components/ConfirmPopover.vue';
    import TrashIcon from '~/components/atoms/icons/TrashIcon.vue';
    import EditIcon from '~/components/atoms/icons/EditIcon.vue';
    import type { ThemeDto } from '~/types/dtos/theme.dto';
    import type { ApiError } from '~/types/apiError';
    import UBadge from '~/components/atoms/UBadge.vue';
    import UPagination from '~/components/UPagination.vue';

    definePageMeta({
        layout: 'backoffice',
        middleware: 'admin',
    });

    const toast = useToast();
    const {
        public: { apiUrl },
    } = useRuntimeConfig();

    const themes = ref<ThemeDto[]>([]);
    const pending = ref(false);
    const isSaving = ref(false);
    const editingTheme = ref<number | null>(null);

    const editingThemeData = ref<Partial<ThemeDto>>({});
    const originalThemeData = ref<Partial<ThemeDto>>({});

    const newTheme = reactive<{ name: string; description: string }>({
        name: '',
        description: '',
    });

    const {
        paginatedItems: paginatedThemes,
        meta,
        goToPage,
        nextPage,
        previousPage,
        firstPage,
        lastPage,
        setItemsPerPage,
    } = usePagination(themes, { itemsPerPage: 9 });

    async function loadThemes() {
        pending.value = true;
        try {
            const { data } = await useAuthFetch<{ themes: ThemeDto[] }>('/api/themes', {
                baseURL: apiUrl,
                transform: (res) => res.themes,
            });

            themes.value = (data.value || []).filter((theme) => theme && theme.id && theme.name && theme.description);
        } catch (err: unknown) {
            const error = err as ApiError;
            toast.error('Erreur', error?.data?.message || 'Erreur lors du chargement des thèmes.');
            console.error(err);
        } finally {
            pending.value = false;
        }
    }

    async function refreshThemes() {
        await loadThemes();
    }

    async function saveNewTheme() {
        if (isSaving.value) return;

        isSaving.value = true;
        try {
            if (!newTheme.name.trim() || !newTheme.description.trim()) {
                toast.error('Erreur', 'Le nom et la description sont requis.');
                return;
            }

            if (newTheme.description.trim().length < 10) {
                toast.error('Erreur', 'La description doit faire au moins 10 caractères.');
                return;
            }

            await useAuthFetch('/api/themes', {
                method: 'POST',
                baseURL: apiUrl,
                body: {
                    name: newTheme.name.trim(),
                    description: newTheme.description.trim(),
                },
            });

            Object.assign(newTheme, { name: '', description: '' });

            await refreshThemes();
            toast.success('Succès', 'Thème ajouté avec succès.');
        } catch (err: unknown) {
            const error = err as ApiError;
            toast.error('Erreur', error?.data?.message || "Erreur lors de l'ajout du thème.");
            console.error(err);
        } finally {
            isSaving.value = false;
        }
    }

    function startEdit(theme: ThemeDto) {
        editingTheme.value = theme.id;
        originalThemeData.value = { ...theme };
        editingThemeData.value = { ...theme };
    }

    async function updateTheme(themeId: number) {
        try {
            if (!editingThemeData.value.name?.trim() || !editingThemeData.value.description?.trim()) {
                toast.error('Erreur', 'Le nom et la description sont requis.');
                return;
            }

            if (editingThemeData.value.description.trim().length < 10) {
                toast.error('Erreur', 'La description doit faire au moins 10 caractères.');
                return;
            }

            await useAuthFetch(`/api/themes/${themeId}`, {
                method: 'PUT',
                baseURL: apiUrl,
                body: {
                    name: editingThemeData.value.name,
                    description: editingThemeData.value.description,
                },
            });

            editingTheme.value = null;
            editingThemeData.value = {};
            originalThemeData.value = {};

            await refreshThemes();
            toast.success('Succès', 'Thème mis à jour.');
        } catch (err: unknown) {
            const error = err as ApiError;
            toast.error('Erreur', error?.data?.message || 'Erreur lors de la mise à jour.');
            console.error(err);
        }
    }

    function cancelEdit() {
        editingTheme.value = null;
        editingThemeData.value = {};
        originalThemeData.value = {};
    }

    async function deleteTheme(id: number) {
        try {
            const { error, status } = await useAuthFetch(`/api/themes/${id}`, {
                method: 'DELETE',
                baseURL: apiUrl,
            });

            if (error.value || (status.value && status.value >= 400)) {
                const message = error.value?.data?.message || 'Erreur lors de la suppression du thème.';
                throw new Error(message);
            }

            themes.value = themes.value.filter((theme) => theme.id !== id);
            toast.success('Succès', 'Thème supprimé avec succès.');
        } catch (err: unknown) {
            console.error(err);
            const error = err as Error;
            toast.error('Erreur', error?.message || 'Erreur lors de la suppression du thème.');
        }
    }

    onMounted(() => {
        loadThemes();
    });
</script>

<template>
    <div class="space-y-6">
        <h2 class="text-2xl font-semibold flex items-center gap-2">
            Thèmes
            <UBadge variant="pill" color="brand" size="md">
                {{ themes.length }}
            </UBadge>
        </h2>
        <div v-if="pending" class="text-gray-600">Chargement…</div>
        <div v-else>
            <div v-if="successMsg" class="text-green-600 text-sm mb-4">{{ successMsg }}</div>
            <div v-if="errorMsg" class="text-red-600 text-sm mb-4">{{ errorMsg }}</div>
            <UCard>
                <template #header>
                    <span class="text-lg font-medium">Ajouter un nouveau thème</span>
                </template>
                <div class="flex flex-col gap-4">
                    <UInput v-model="newTheme.name" label="Nom" type="text" required />
                    <UTextarea v-model="newTheme.description" label="Description" required />
                    <UButton
                        :is-loading="isSaving"
                        :disabled="isSaving || !newTheme.name.trim() || !newTheme.description.trim()"
                        class="w-fit justify-self-start"
                        @click="saveNewTheme"
                    >
                        {{ isSaving ? 'Ajout en cours...' : 'Ajouter' }}
                    </UButton>
                </div>
            </UCard>
            <div>
                <h2 class="text-lg font-semibold my-4">Tous les thèmes</h2>
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <UCard v-for="theme in paginatedThemes" :key="theme.id">
                        <template #header>
                            <div class="flex justify-between items-center">
                                <div class="font-semibold">{{ theme.name }}</div>
                                <div class="flex items-center gap-2">
                                    <button @click="startEdit(theme)">
                                        <EditIcon class="w-5 h-5 text-blue-500 hover:text-blue-700" />
                                    </button>
                                    <ConfirmPopover :item-name="theme.name" @confirm="deleteTheme(theme.id)">
                                        <template #trigger>
                                            <button class="text-red-500 hover:text-red-700">
                                                <TrashIcon class="w-5 h-5" />
                                            </button>
                                        </template>
                                    </ConfirmPopover>
                                </div>
                            </div>
                        </template>
                        <template v-if="editingTheme === theme.id">
                            <div class="space-y-3">
                                <UInput v-model="editingThemeData.name" label="Nom" type="text" />
                                <UTextarea v-model="editingThemeData.description" label="Description" />
                                <div class="flex gap-2">
                                    <UButton @click="updateTheme(theme.id)">Enregistrer</UButton>
                                    <UButton variant="ghost" @click="cancelEdit()">Annuler</UButton>
                                </div>
                            </div>
                        </template>
                        <template v-else>
                            <div class="space-y-2">
                                <p><strong>Nom :</strong> {{ theme.name }}</p>
                                <p><strong>Description :</strong> {{ theme.description }}</p>
                            </div>
                        </template>
                    </UCard>
                </div>

                <UPagination
                    :meta="meta"
                    @go-to-page="goToPage"
                    @next-page="nextPage"
                    @previous-page="previousPage"
                    @first-page="firstPage"
                    @last-page="lastPage"
                    @set-items-per-page="setItemsPerPage"
                />
            </div>
        </div>
    </div>
</template>
