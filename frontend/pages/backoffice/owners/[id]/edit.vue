<script setup lang="ts">
    import { ref, reactive, watch } from 'vue';
    import { useRoute } from 'vue-router';
    import Input from '~/components/atoms/UInput.vue';
    import { useRuntimeConfig } from '#app';
    import { useAuthFetch } from '~/composables/useAuthFetch';
    import type { OwnerDto } from '~/types/dtos/owner.dto';

    definePageMeta({
        layout: 'backoffice',
        middleware: 'admin',
    });

    const route = useRoute();
    const id = ref<string | undefined>(undefined);
    const owner = ref<OwnerDto | null>(null);
    const pending = ref(false);
    const errorMsg = ref('');
    const success = ref(false);
    const saving = ref(false);

    const {
        public: { apiUrl },
    } = useRuntimeConfig();

    const form = reactive({
        firstName: '',
        lastName: '',
        email: '',
        phone: '',
        isVerified: false,
    });

    async function loadOwner(ownerId: string) {
        pending.value = true;
        errorMsg.value = '';
        try {
            const response = await useAuthFetch(`/api/owners/${ownerId}`, {
                baseURL: apiUrl,
            });
            owner.value = response.data.value;
            if (owner.value) {
                form.firstName = owner.value.firstName;
                form.lastName = owner.value.lastName;
                form.email = owner.value.email;
                form.phone = owner.value.phone;
                form.isVerified = owner.value.isVerified;
            } else {
                errorMsg.value = 'Aucun hôte trouvé.';
            }
        } catch (e: unknown) {
            errorMsg.value = e?.data?.message || 'Erreur lors du chargement.';
        } finally {
            pending.value = false;
        }
    }

    async function refresh() {
        if (id.value) {
            await loadOwner(id.value);
        }
    }

    async function save() {
        if (!id.value) return;

        saving.value = true;
        success.value = false;
        errorMsg.value = '';

        try {
            await $fetch(`/api/owners/${id.value}`, {
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
        } catch (error: unknown) {
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
                loadOwner(newId);
            }
        },
        { immediate: true }
    );
</script>

<template>
    <div class="max-w-3xl space-y-6">
        <h1 class="text-2xl font-semibold">Modifier l’hôte</h1>

        <div v-if="pending" class="text-gray-600">Chargement…</div>
        <div v-else-if="errorMsg" class="text-red-600">{{ errorMsg }}</div>
        <form v-else class="grid gap-6 pt-6 md:grid-cols-2" :aria-busy="saving || pending" @submit.prevent="save">
            <Input v-model="form.firstName" label="Prénom" name="firstName" type="text" required />
            <Input v-model="form.lastName" label="Nom" name="lastName" type="text" required />
            <Input v-model="form.email" class="md:col-span-2" label="Email" name="email" type="email" required />
            <Input v-model="form.phone" class="md:col-span-2" label="Téléphone" name="phone" type="tel" />

            <div class="md:col-span-2 flex flex-col gap-2 sm:flex-row sm:justify-between sm:items-center">
                <UButton :disabled="saving" :is-loading="saving" size="lg" variant="primary" type="submit">
                    {{ saving ? 'Enregistrement…' : 'Enregistrer' }}
                </UButton>

                <span v-if="success" class="text-green-600 text-sm">Modifications enregistrées</span>
            </div>
        </form>
    </div>
</template>
