<script setup lang="ts">
    import { ref, reactive, watch } from 'vue';
    import { useRoute } from 'vue-router';
    import Input from '~/components/atoms/UInput.vue';
    import UCheckbox from '~/components/atoms/UCheckbox.vue';
    import UButton from '~/components/atoms/UButton.vue';
    import { useRuntimeConfig } from '#app';
    import { useAuthFetch } from '~/composables/useAuthFetch';
    import { useToast } from '~/composables/useToast';
    import type { AdminDto } from '~/types/dtos/admin.dto';
    import type { ApiError } from '~/types/apiError';
    import type { FormData } from '~/types/formData';

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
    const admin = ref<AdminDto | null>(null);
    const pending = ref(false);
    const errorMsg = ref('');
    const saving = ref(false);
    const success = ref(false);

    const form = reactive<FormData>({
        firstName: '',
        lastName: '',
        email: '',
        phone: '',
        address: '',
        avatar: '',
        isVerified: false,
    });

    async function loadAdmin(adminId: string) {
        pending.value = true;
        errorMsg.value = '';
        try {
            const { data } = await useAuthFetch<{ value: AdminDto }>(`/api/admins/${adminId}`, {
                baseURL: apiUrl,
            });

            const adminData = data.value ?? null;
            admin.value = adminData;

            if (adminData) {
                form.firstName = adminData.firstName;
                form.lastName = adminData.lastName;
                form.email = adminData.email;
                form.phone = adminData.phone ?? '';
                form.address = adminData.address ?? '';
                form.avatar = adminData.avatar ?? '';
                form.isVerified = adminData.isVerified;
            } else {
                errorMsg.value = 'Administrateur introuvable.';
            }
        } catch (err: unknown) {
            const error = err as ApiError;
            errorMsg.value = error?.data?.message ?? 'Erreur lors du chargement.';
            console.error(err);
        } finally {
            pending.value = false;
        }
    }

    async function refresh() {
        if (id.value) {
            await loadAdmin(id.value);
        }
    }

    async function save() {
        if (!id.value) return;

        saving.value = true;
        success.value = false;
        errorMsg.value = '';

        try {
            await useAuthFetch(`/api/admins/${id.value}`, {
                method: 'PUT',
                baseURL: apiUrl,
                body: {
                    firstName: form.firstName,
                    lastName: form.lastName,
                    email: form.email,
                    phone: form.phone,
                    address: form.address,
                    isVerified: form.isVerified,
                },
            });

            success.value = true;
            toast.success('Succès', 'Modifications enregistrées');
            await refresh();
        } catch (err: unknown) {
            const error = err as ApiError;
            errorMsg.value = error?.data?.message ?? 'Erreur lors de l’enregistrement.';
        } finally {
            saving.value = false;
        }
    }

    watch(
        () => route.params.id,
        (newId) => {
            if (typeof newId === 'string' && newId !== '') {
                id.value = newId;
                loadAdmin(newId);
            }
        },
        { immediate: true }
    );
</script>

<template>
    <div class="max-w-3xl space-y-6">
        <ULink to="/backoffice/admins" size="lg" class="flex flex-row gap-2">
            <ArrowLeftIcon /> Retour à la liste
        </ULink>
        <h1 class="text-2xl font-semibold">Modifier l’administrateur</h1>

        <div v-if="pending" class="text-gray-600">Chargement…</div>
        <div v-else>
            <form
                v-if="admin"
                class="grid pt-6 gap-6 md:grid-cols-2"
                :aria-busy="saving || pending"
                @submit.prevent="save"
            >
                <Input v-model="form.firstName" label="Prénom" name="firstName" type="text" required />
                <Input v-model="form.lastName" label="Nom" name="lastName" type="text" required />

                <Input v-model="form.email" class="md:col-span-2" label="Email" name="email" type="email" required />
                <Input v-model="form.phone" class="md:col-span-2" label="Téléphone" name="phone" type="tel" />

                <Input v-model="form.address" class="md:col-span-2" label="Adresse" name="address" type="text" />

                <div class="md:col-span-2 flex flex-col gap-3">
                    <label class="text-body-sm">Avatar</label>
                    <AdminAvatarUpload 
                        :current-avatar="admin?.avatar" 
                        :user-id="id" 
                        user-type="admins"
                        :user-name="`${admin?.firstName} ${admin?.lastName}`"
                        @avatar-updated="refresh" 
                    />
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
            <div v-else class="text-gray-600">Administrateur introuvable ou non chargé.</div>
        </div>
    </div>
</template>
