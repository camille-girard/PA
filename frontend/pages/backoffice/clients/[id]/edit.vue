<script setup lang="ts">
    import { ref, reactive, watch } from 'vue';
    import { useRoute } from 'vue-router';
    import Input from '~/components/atoms/UInput.vue';
    import UCheckbox from '~/components/atoms/UCheckbox.vue';
    import UTextarea from '~/components/atoms/UTextarea.vue';
    import UButton from '~/components/atoms/UButton.vue';
    import { useRuntimeConfig } from '#app';
    import { useAuthFetch } from '~/composables/useAuthFetch';
    import { useToast } from '~/composables/useToast';
    import type { Client } from '~/types/client';

    interface ApiError {
        data?: {
            message?: string;
            status?: number;
        };
        message?: string;
    }

    definePageMeta({
        layout: 'backoffice',
        middleware: 'admin',
    });

    const toast = useToast();
    const route = useRoute();

    const {
        public: { apiUrl },
    } = useRuntimeConfig();

    const id = ref<string | undefined>(undefined);
    const client = ref<Client | null>(null);
    const pending = ref(false);
    const saving = ref(false);
    const errorMsg = ref('');
    const success = ref(false);

    const availableRoles = [
        { label: 'Client', value: 'ROLE_CLIENT' },
        { label: 'Hôte', value: 'ROLE_OWNER' },
        { label: 'Admin', value: 'ROLE_ADMIN' },
    ];

    const form = reactive({
        firstName: '',
        lastName: '',
        email: '',
        phone: '',
        address: '',
        avatar: '',
        isVerified: false,
        role: 'ROLE_CLIENT',
        preferences: '' as string,
    });

    async function loadClient(clientId: string) {
        pending.value = true;
        errorMsg.value = '';
        try {
            const { data } = await useAuthFetch<{ client: Client }>(`/api/clients/${clientId}`, {
                baseURL: apiUrl,
            });
            client.value = data.value ?? null;

            if (client.value) {
                form.firstName = client.value.firstName;
                form.lastName = client.value.lastName;
                form.email = client.value.email;
                form.phone = client.value.phone ?? '';
                form.address = client.value.address ?? '';
                form.avatar = client.value.avatar ?? '';
                form.isVerified = Boolean(client.value?.isVerified);
                form.role = client.value.roles?.[0] ?? 'ROLE_CLIENT';
                form.preferences = (client.value.preferences ?? []).join(', ');
            } else {
                errorMsg.value = 'Client introuvable.';
            }
        } catch (err: unknown) {
            const error = err as { data?: { message?: string } };
            errorMsg.value = error?.data?.message || 'Erreur lors du chargement.';
            console.error(err);
        } finally {
            pending.value = false;
        }
    }

    async function refresh() {
        if (id.value) {
            await loadClient(id.value);
        }
    }
    async function save() {
        if (!id.value) return;

        saving.value = true;
        success.value = false;
        errorMsg.value = '';

        try {
            const preferencesArray = form.preferences
                .split(',')
                .map((p) => p.trim())
                .filter((p) => p.length > 0);

            const response = await useAuthFetch(`/api/clients/${id.value}`, {
                method: 'PUT',
                baseURL: apiUrl,
                body: {
                    firstName: form.firstName,
                    lastName: form.lastName,
                    email: form.email,
                    phone: form.phone,
                    address: form.address,
                    avatar: form.avatar,
                    isVerified: form.isVerified,
                    role: form.role,
                    preferences: preferencesArray,
                },
            });

            if (response.error?.value) {
                const message = response.error.value.data?.message || 'Erreur lors de l’enregistrement.';

                errorMsg.value = message;
                toast.error('Erreur', message);

                console.error('Erreur useAuthFetch:', response.error.value);
                return;
            }

            const returned = response.data?.value;
            const apiStatus = returned?.status ?? 200;

            if (apiStatus >= 400) {
                const apiMessage = returned?.message || 'Erreur lors de l’enregistrement.';
                errorMsg.value = apiMessage;
                toast.error('Erreur', apiMessage);
                return;
            }

            success.value = true;
            toast.success('Succès', 'Modifications enregistrées');
            await refresh();
        } catch (err: unknown) {
            if (typeof err === 'object' && err !== null && 'data' in err) {
                const apiErr = err as ApiError;
                const message = apiErr.data?.message || 'Erreur inattendue lors de l’enregistrement.';
                errorMsg.value = message;
                toast.error('Erreur', message);
            } else {
                errorMsg.value = 'Erreur inattendue lors de l’enregistrement.';
                toast.error('Erreur', errorMsg.value);
            }
        } finally {
            saving.value = false;
        }
    }

    watch(
        () => route.params.id,
        (newId) => {
            if (typeof newId === 'string' && newId !== '') {
                id.value = newId;
                loadClient(newId);
            }
        },
        { immediate: true }
    );
</script>

<template>
    <div class="max-w-3xl space-y-6">
        <ULink to="/backoffice/clients" size="lg" class="flex flex-row gap-2">
            <ArrowLeftIcon /> Retour à la liste
        </ULink>
        <h1 class="text-2xl font-semibold">Modifier le client</h1>

        <div v-if="pending" class="text-gray-600">Chargement…</div>
        <div v-else>
            <form
                v-if="client"
                class="grid pt-6 gap-6 md:grid-cols-2"
                :aria-busy="saving || pending"
                @submit.prevent="save"
            >
                <Input v-model="form.firstName" label="Prénom" name="firstName" type="text" required />
                <Input v-model="form.lastName" label="Nom" name="lastName" type="text" required />
                <Input v-model="form.email" class="md:col-span-2" label="Email" name="email" type="email" required />
                <Input v-model="form.phone" class="md:col-span-2" label="Téléphone" name="phone" type="tel" />
                <Input v-model="form.address" class="md:col-span-2" label="Adresse" name="address" type="text" />
                <Input v-model="form.avatar" class="md:col-span-2" label="Avatar (URL)" name="avatar" type="url" />

                <UTextarea
                    v-model="form.preferences"
                    class="md:col-span-2"
                    label="Préférences (séparées par des virgules)"
                    placeholder="exemple: Wifi, Parking, Animaux acceptés"
                />

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-secondary mb-1" for="role">Rôle</label>
                    <select
                        id="role"
                        v-model="form.role"
                        required
                        class="border bg-primary rounded-lg shadow-xs px-3.5 py-3 focus:ring-2 focus:border-transparent focus:outline-none text-primary w-full"
                    >
                        <option disabled value="">Choisir un rôle</option>
                        <option v-for="role in availableRoles" :key="role.value" :value="role.value">
                            {{ role.label }}
                        </option>
                    </select>
                </div>

                <UCheckbox v-model="form.isVerified" class="md:col-span-2" label="Compte vérifié" name="isVerified" />

                <div class="md:col-span-2 flex flex-col gap-2 sm:flex-row sm:justify-between sm:items-center">
                    <UButton :disabled="saving" :is-loading="saving" size="lg" variant="primary" type="submit">
                        {{ saving ? 'Enregistrement…' : 'Enregistrer' }}
                    </UButton>

                    <span v-if="success" class="text-green-600 text-sm">Modifications enregistrées</span>
                </div>
            </form>

            <div v-else-if="errorMsg" class="text-red-600">{{ errorMsg }}</div>
            <div v-else class="text-gray-600">Client introuvable ou non chargé.</div>
        </div>
    </div>
</template>
