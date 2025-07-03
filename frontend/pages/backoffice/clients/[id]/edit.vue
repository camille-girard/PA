<script setup lang="ts">
    import { ref, reactive, watch } from 'vue';
    import { useRoute } from 'vue-router';
    import Input from '~/components/atoms/UInput.vue';
    import { useRuntimeConfig } from '#app';
    import { useAuthFetch } from '~/composables/useAuthFetch';

    definePageMeta({
        layout: 'backoffice',
        middleware: 'admin',
    });

    const route = useRoute();

    const {
        public: { apiUrl },
    } = useRuntimeConfig();

    const id = ref<string | undefined>(undefined);
    const client = ref<any>(null);
    const pending = ref(false);
    const errorMsg = ref('');
    const saving = ref(false);
    const success = ref(false);

    const form = reactive({
        firstName: '',
        lastName: '',
        email: '',
        phone: '',
        isVerified: false,
    });

    async function loadClient(clientId: string) {
        pending.value = true;
        errorMsg.value = '';
        try {
            const { data } = await useAuthFetch<{ client: any }>(`/api/clients/${clientId}`, {
                baseURL: apiUrl,
            });
            client.value = data.value?.client ?? null;

            if (client.value) {
                form.firstName = client.value.firstName;
                form.lastName = client.value.lastName;
                form.email = client.value.email;
                form.phone = client.value.phone;
                form.isVerified = client.value.isVerified;
            } else {
                errorMsg.value = 'Client introuvable.';
            }
        } catch (error: any) {
            errorMsg.value = error?.data?.message || 'Erreur lors du chargement.';
            console.error(error);
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
            await useAuthFetch(`/api/clients/${id.value}`, {
                method: 'PUT',
                baseURL: apiUrl,
                body: {
                    firstName: form.firstName,
                    lastName: form.lastName,
                    email: form.email,
                    phone: form.phone,
                    isVerified: form.isVerified,
                },
            });

            success.value = true;
            await refresh();
        } catch (error: any) {
            errorMsg.value = error?.data?.message || 'Erreur lors de l’enregistrement.';
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
        <h1 class="text-2xl font-semibold">Modifier le client</h1>

        <div v-if="pending" class="text-gray-600">Chargement…</div>
        <div v-else>
            <form
                v-if="client"
                @submit.prevent="save"
                class="grid pt-6 gap-6 md:grid-cols-2"
                :aria-busy="saving || pending"
            >
                <Input v-model="form.firstName" label="Prénom" name="firstName" type="text" required />
                <Input v-model="form.lastName" label="Nom" name="lastName" type="text" required />
                <Input class="md:col-span-2" v-model="form.email" label="Email" name="email" type="email" required />
                <Input class="md:col-span-2" v-model="form.phone" label="Téléphone" name="phone" type="tel" />

                <div class="md:col-span-2 flex flex-col gap-2 sm:flex-row sm:justify-between sm:items-center">
                    <UButton :disabled="saving" :isLoading="saving" size="lg" variant="primary" type="submit">
                        {{ saving ? 'Enregistrement…' : 'Enregistrer' }}
                    </UButton>

                    <span v-if="success" class="text-green-600 text-sm">Modifications enregistrées</span>
                    <span v-if="errorMsg" class="text-red-600 text-sm">{{ errorMsg }}</span>
                </div>
            </form>

            <div v-else-if="errorMsg" class="text-red-600">{{ errorMsg }}</div>
            <div v-else class="text-gray-600">Client introuvable ou non chargé.</div>
        </div>
    </div>
</template>
