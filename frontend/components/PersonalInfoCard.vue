<script setup lang="ts">
    import { useAuthStore } from '@/stores/auth';
    import { AccommodationAdvantage } from '~/types/accommodationAdvantage';

    const auth = useAuthStore();
    const router = useRouter();

    onMounted(() => {
        if (!auth.user) {
            auth.fetchUser();
        }
    });

    function saveField(field: string, value: string) {
        auth.updateUser({ [field]: value });
    }

    function savePreferences(preferences: AccommodationAdvantage[]) {
        auth.updateUser({ preferences });
    }

    function handleDelete() {
        const confirmDelete = confirm('Es-tu sûr de vouloir supprimer ton compte ? Cette action est irréversible.');
        if (!confirmDelete) return;

        auth.deleteAccount().then((res) => {
            if (res.success) {
                navigateTo('/login');
            } else {
                alert(res.error);
            }
        });
    }

    function goToNewAccommodation() {
        router.push('/my-accommodation/create');
    }

    function goToBecomeOwner() {
        router.push('/owner-request');
    }

    const isClientOnly = computed(
        () => auth.user?.roles.includes('ROLE_CLIENT') && !auth.user?.roles.includes('ROLE_OWNER')
    );

    const userPreferences = computed(() => {
        if (!auth.user?.preferences) {
            return [];
        }

        if (!Array.isArray(auth.user.preferences)) {
            if (typeof auth.user.preferences === 'string') {
                try {
                    const parsed = JSON.parse(auth.user.preferences);
                    if (Array.isArray(parsed)) {
                        return parsed.filter((pref) => Object.values(AccommodationAdvantage).includes(pref));
                    }
                } catch {
                    return auth.user.preferences
                        .split(',')
                        .map((pref) => pref.trim())
                        .filter((pref) =>
                            Object.values(AccommodationAdvantage).includes(pref as AccommodationAdvantage)
                        ) as AccommodationAdvantage[];
                }
            }
            return [];
        }

        return auth.user.preferences.filter((pref) => Object.values(AccommodationAdvantage).includes(pref));
    });

    const userAvatarUrl = computed(() => {
        return auth.user?.avatar || '';
    });

    const onAvatarUpdated = (avatarUrl: string) => {
        console.log('Avatar updated:', avatarUrl);
        // L'avatar sera automatiquement mis à jour via le store
    };

    const onAvatarDeleted = () => {
        console.log('Avatar deleted');
        // L'avatar sera automatiquement mis à jour via le store
    };
</script>

<template>
    <div class="max-w-5xl mx-auto p-6 flex flex-col md:flex-row gap-10 items-start">
        <div class="bg-orange-100 rounded-3xl p-6 flex flex-col items-center w-full md:w-1/4">
            <div class="mb-4">
                <AvatarUpload
                    size="2xl"
                    :current-avatar="userAvatarUrl"
                    @avatar-updated="onAvatarUpdated"
                    @avatar-deleted="onAvatarDeleted"
                />
            </div>
            <p class="text-body-lg font-bold">{{ auth.user?.firstName }}</p>
        </div>

        <div class="w-full md:w-3/4 space-y-6">
            <div v-if="auth.user" class="space-y-4">
                <EditableField
                    label="Nom officiel"
                    :model-value="auth.user.lastName"
                    field="lastName"
                    @save="saveField"
                />

                <EditableField label="Prénom" :model-value="auth.user.firstName" field="firstName" @save="saveField" />

                <EditableField label="Adresse e-mail" :model-value="auth.user.email" field="email" @save="saveField" />

                <EditableField
                    v-if="auth.user?.roles.includes('ROLE_OWNER')"
                    label="Bio"
                    :model-value="auth.user.bio ?? ''"
                    field="bio"
                    @save="saveField"
                />

                <EditableField
                    label="Numéro de téléphone"
                    :model-value="auth.user.phone ?? ''"
                    field="phone"
                    @save="saveField"
                />

                <EditableField
                    label="Adresse"
                    :model-value="auth.user.address ?? ''"
                    field="address"
                    @save="saveField"
                />

                <EditablePreferences
                    v-if="!auth.user?.roles.includes('ROLE_OWNER')"
                    label="Préférences"
                    :model-value="userPreferences"
                    @save="savePreferences"
                />
                
                <!-- Section Authentification à deux facteurs -->
                <div class="border-t pt-6 mt-6">
                    <TwoFactorAuthSetup />
                </div>
            </div>
        </div>
    </div>
    <div class="flex flex-col items-center mt-10 gap-2">
        <UButton
            v-if="auth.user?.roles.includes('ROLE_OWNER')"
            size="lg"
            variant="primary"
            class="w-full max-w-2xl"
            @click="goToNewAccommodation"
        >
            Ajouter un nouveau bien
        </UButton>

        <UButton v-else-if="isClientOnly" size="lg" variant="primary" class="w-full max-w-2xl" @click="goToBecomeOwner">
            Devenir propriétaire
        </UButton>

        <UButton
            size="sm"
            variant="secondary"
            class="text-red-600 border-orange-300 hover:bg-orange-50 hover:text-red-700"
            @click="handleDelete"
        >
            Supprimer mon compte
        </UButton>
    </div>
</template>
