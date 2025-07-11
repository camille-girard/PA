<script setup lang="ts">
    import { ref } from 'vue';
    import { useAuthStore } from '~/stores/auth';
    import { useAuthFetch } from '~/composables/useAuthFetch';

    definePageMeta({
        layout: 'backoffice',
        middleware: 'admin',
    });

    const authStore = useAuthStore();

    const firstName = ref(authStore.user?.firstName || '');
    const lastName = ref(authStore.user?.lastName || '');
    const email = ref(authStore.user?.email || '');
    const phone = ref(authStore.user?.phone || '');
    const address = ref(authStore.user?.address || '');

    const savingProfile = ref(false);
    const successProfile = ref('');
    const errorProfile = ref('');

    async function saveProfile() {
        successProfile.value = '';
        errorProfile.value = '';
        savingProfile.value = true;

        try {
            const {
                public: { apiUrl },
            } = useRuntimeConfig();

            const { data, error } = await useAuthFetch<typeof authStore.user>(`${apiUrl}/api/me`, {
                method: 'PUT',
                body: {
                    firstName: firstName.value,
                    lastName: lastName.value,
                    email: email.value,
                    phone: phone.value,
                    address: address.value,
                },
                credentials: 'include',
            });

            if (error.value) {
                errorProfile.value = error.value?.data?.message || 'Erreur lors de la mise à jour';
            } else if (data.value) {
                authStore.user = data.value;
                successProfile.value = 'Profil mis à jour avec succès !';
            }
        } catch (err) {
            errorProfile.value = 'Erreur inattendue.';
            console.error(err);
        } finally {
            savingProfile.value = false;
        }
    }

    const oldPassword = ref('');
    const newPassword = ref('');

    const savingPassword = ref(false);
    const successPassword = ref('');
    const errorPassword = ref('');

    async function changePassword() {
        successPassword.value = '';
        errorPassword.value = '';
        savingPassword.value = true;

        try {
            const {
                public: { apiUrl },
            } = useRuntimeConfig();

            const { error } = await useAuthFetch(`${apiUrl}/api/me/password`, {
                method: 'PUT',
                body: {
                    oldPassword: oldPassword.value,
                    newPassword: newPassword.value,
                },
                credentials: 'include',
            });

            if (error.value) {
                errorPassword.value = error.value?.data?.message || 'Erreur lors du changement de mot de passe';
            } else {
                successPassword.value = 'Mot de passe mis à jour avec succès !';
                oldPassword.value = '';
                newPassword.value = '';
            }
        } catch (err) {
            errorPassword.value = 'Erreur inattendue.';
            console.error(err);
        } finally {
            savingPassword.value = false;
        }
    }
</script>

<template>
    <div class="space-y-12 max-w-3xl">
        <h1 class="text-2xl font-semibold">Mon profil</h1>

        <form class="grid gap-6 pt-6 md:grid-cols-2" @submit.prevent="saveProfile">
            <div>
                <label class="block text-sm font-medium mb-1">Prénom *</label>
                <input
                    v-model="firstName"
                    type="text"
                    required
                    class="w-full border rounded px-3 py-2 focus:outline-none focus:ring focus:border-brand-600"
                />
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Nom *</label>
                <input
                    v-model="lastName"
                    type="text"
                    required
                    class="w-full border rounded px-3 py-2 focus:outline-none focus:ring focus:border-brand-600"
                />
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-medium mb-1">Email *</label>
                <input
                    v-model="email"
                    type="email"
                    required
                    class="w-full border rounded px-3 py-2 focus:outline-none focus:ring focus:border-brand-600"
                />
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Téléphone</label>
                <input
                    v-model="phone"
                    type="tel"
                    class="w-full border rounded px-3 py-2 focus:outline-none focus:ring focus:border-brand-600"
                />
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-medium mb-1">Adresse</label>
                <input
                    v-model="address"
                    type="text"
                    class="w-full border rounded px-3 py-2 focus:outline-none focus:ring focus:border-brand-600"
                />
            </div>

            <div class="md:col-span-2 flex gap-3 items-center">
                <button
                    :disabled="savingProfile"
                    class="px-4 py-2 rounded bg-brand-600 text-white hover:bg-brand-700 disabled:opacity-50"
                >
                    {{ savingProfile ? 'Enregistrement...' : 'Enregistrer' }}
                </button>
                <span v-if="successProfile" class="text-green-600">{{ successProfile }}</span>
                <span v-if="errorProfile" class="text-red-600">{{ errorProfile }}</span>
            </div>
        </form>

        <div class="space-y-4 border-t pt-6">
            <h2 class="text-xl font-semibold">Changer mon mot de passe</h2>
            <form class="grid gap-6 md:grid-cols-2" @submit.prevent="changePassword">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium mb-1">Mot de passe actuel *</label>
                    <input
                        v-model="oldPassword"
                        type="password"
                        required
                        class="w-full border rounded px-3 py-2 focus:outline-none focus:ring focus:border-brand-600"
                    />
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium mb-1">Nouveau mot de passe *</label>
                    <input
                        v-model="newPassword"
                        type="password"
                        required
                        class="w-full border rounded px-3 py-2 focus:outline-none focus:ring focus:border-brand-600"
                    />
                </div>

                <div class="md:col-span-2 flex gap-3 items-center">
                    <button
                        :disabled="savingPassword"
                        class="px-4 py-2 rounded bg-brand-600 text-white hover:bg-brand-700 disabled:opacity-50"
                    >
                        {{ savingPassword ? 'Enregistrement...' : 'Changer le mot de passe' }}
                    </button>
                    <span v-if="successPassword" class="text-green-600">{{ successPassword }}</span>
                    <span v-if="errorPassword" class="text-red-600">{{ errorPassword }}</span>
                </div>
            </form>
        </div>
    </div>
</template>
