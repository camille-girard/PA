<script setup lang="ts">
    import { ref, computed } from 'vue';
    import { useAuthStore } from '~/stores/auth';
    import { useToast } from '~/composables/useToast';

    interface Props {
        isOpen: boolean;
        currentAvatar?: string | null;
    }

    interface Emits {
        (e: 'close' | 'avatar-deleted'): void;
        (e: 'avatar-updated', avatarUrl: string): void;
    }

    withDefaults(defineProps<Props>(), {
        currentAvatar: null,
    });

    const emit = defineEmits<Emits>();

    const { success, error } = useToast();
    const authStore = useAuthStore();

    const previewUrl = ref<string | null>(null);
    const isUploading = ref(false);
    const uploadProgress = ref(0);

    const { $api } = useNuxtApp();
    const uploadUrl = computed(() => $api('/api/me/avatar'));

    const userInitials = computed(() => {
        if (!authStore.user) return '';

        const { firstName, lastName } = authStore.user;

        if (firstName && lastName) {
            return `${firstName.charAt(0)}${lastName.charAt(0)}`.toUpperCase();
        }

        if (firstName) {
            return firstName.charAt(0).toUpperCase();
        }

        return '';
    });

    const handleFileSelect = (file: File) => {
        if (!file) return;

        const allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
        if (!allowedTypes.includes(file.type)) {
            error('Format incorrect', 'Utilisez JPEG, PNG ou WebP');
            return;
        }

        const maxSize = 5 * 1024 * 1024;
        if (file.size > maxSize) {
            error('Fichier trop volumineux', 'Taille maximale : 5MB');
            return;
        }

        const reader = new FileReader();
        reader.onload = (e) => {
            previewUrl.value = e.target?.result as string;
        };
        reader.readAsDataURL(file);

        isUploading.value = true;
    };

    const handleUploadSuccess = async (data: { response?: { avatar?: string } }) => {
        isUploading.value = false;

        if (data.response?.avatar) {
            emit('avatar-updated', data.response.avatar);
            await authStore.fetchUser();
            success('Avatar mis à jour', 'Votre avatar a été mis à jour avec succès');
            emit('close');
        }
    };

    const handleUploadError = () => {
        isUploading.value = false;
        previewUrl.value = null;
        error("Erreur d'upload", "Impossible de télécharger l'avatar");
    };

    const handleProgress = (data: { progress: number }) => {
        uploadProgress.value = data.progress;
    };

    const handleDeleteAvatar = async () => {
        try {
            const { data, error } = await useAuthFetch($api('/api/me/avatar'), {
                method: 'DELETE',
            });

            if (data.value && !error.value) {
                emit('avatar-deleted');
                await authStore.fetchUser();
                success('Avatar supprimé', 'Votre avatar a été supprimé');
                emit('close');
            }
        } catch {
            error('Erreur de suppression', "Impossible de supprimer l'avatar");
        }
    };
</script>

<template>
    <UBaseModal :is-open="isOpen" @close="$emit('close')">
        <div class="p-6">
            <div class="text-center mb-6">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Changer l'avatar</h2>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Sélectionnez une nouvelle image pour votre avatar
                </p>
            </div>

            <div class="flex justify-center mb-6">
                <UAvatar
                    :image-src="previewUrl || currentAvatar || undefined"
                    :text="userInitials"
                    size="xl"
                    class="ring-4 ring-white"
                />
            </div>

            <div class="mb-6">
                <UFileInput
                    accept="image/jpeg,image/png,image/webp"
                    :upload-url="uploadUrl"
                    label="Sélectionner une image"
                    drag-text="ou glisser-déposer"
                    format-text="JPEG, PNG, WebP"
                    max-size-text="(max. 5MB)"
                    @update:file="handleFileSelect"
                    @upload:success="handleUploadSuccess"
                    @upload:error="handleUploadError"
                    @update:progress="handleProgress"
                />
            </div>

            <div class="text-xs text-gray-500 dark:text-gray-400 mb-6 text-center">
                <p>• Formats acceptés : JPEG, PNG, WebP</p>
                <p>• Taille maximale : 5MB</p>
                <p>• Recommandé : 200x200px minimum</p>
            </div>

            <div class="flex gap-3">
                <UButton
                    v-if="currentAvatar"
                    variant="primary"
                    size="sm"
                    class="flex-1"
                    :disabled="isUploading"
                    @click="handleDeleteAvatar"
                >
                    <TrashIcon class="w-4 h-4 mr-2" />
                    Supprimer
                </UButton>

                <UButton variant="secondary" size="sm" class="flex-1" :disabled="isUploading" @click="$emit('close')">
                    Annuler
                </UButton>
            </div>

            <div v-if="isUploading" class="mt-4">
                <div class="flex items-center justify-between text-sm text-gray-600 dark:text-gray-400 mb-2">
                    <span>Upload en cours...</span>
                    <span>{{ Math.round(uploadProgress) }}%</span>
                </div>
                <UProgress :value="uploadProgress" />
            </div>
        </div>
    </UBaseModal>
</template>
