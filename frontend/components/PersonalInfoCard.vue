<script setup lang="ts">
    import UButton from '~/components/atoms/UButton.vue';
    import UAvatar from '~/components/atoms/UAvatar.vue';
    import EditableField from '@/components/EditableField.vue';
    import { useAuthStore } from '@/stores/auth';

    const auth = useAuthStore();
    const router = useRouter();

    onMounted(() => {
        if (!auth.user) {
            auth.fetchUser();
        }
    });

    function saveField(field: string, value: string) {
        if (field === 'preferences') {
            const preferencesArray = value
                .split(',')
                .map((pref) => pref.trim())
                .filter((pref) => pref.length > 0);
            auth.updateUser({ [field]: preferencesArray });
        } else {
            auth.updateUser({ [field]: value });
        }
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
        router.push('/newaccommodation');
    }

    function goToBecomeOwner() {
        router.push('/owner-request');
    }

    const isClientOnly = computed(
        () => auth.user?.roles.includes('ROLE_CLIENT') && !auth.user?.roles.includes('ROLE_OWNER')
    );

    const preferencesString = computed(() => {
        if (!auth.user?.preferences) {
            return '';
        }

        if (!Array.isArray(auth.user.preferences)) {
            if (typeof auth.user.preferences === 'string') {
                try {
                    const parsed = JSON.parse(auth.user.preferences);
                    if (Array.isArray(parsed)) {
                        return parsed.join(', ');
                    }
                } catch {
                    return auth.user.preferences; // Retourner tel quel si c'est déjà une string
                }
            }
            return '';
        }

        return auth.user.preferences.join(', ');
    });

    const userInitials = computed(() => {
        if (!auth.user) return '';

        const { firstName, lastName } = auth.user;

        if (firstName && lastName) {
            return `${firstName.charAt(0)}${lastName.charAt(0)}`.toUpperCase();
        }

        if (firstName) {
            return firstName.charAt(0).toUpperCase();
        }

        return '';
    });

    const userAvatarUrl = computed(() => {
        return auth.user?.avatar || '';
    });
</script>

<template>
    <div class="max-w-5xl mx-auto p-6 flex flex-col md:flex-row gap-10 items-start">
        <div class="bg-orange-100 rounded-3xl p-6 flex flex-col items-center w-full md:w-1/4">
            <div class="mb-4">
                <UAvatar size="2xl" :image-src="userAvatarUrl" :text="userInitials" status-icon="false" />
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

                <EditableField
                    label="Préférences"
                    :model-value="preferencesString"
                    field="preferences"
                    @save="saveField"
                />
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
