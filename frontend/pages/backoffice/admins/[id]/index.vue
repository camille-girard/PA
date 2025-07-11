<script setup lang="ts">
    import { ref, watch } from 'vue';
    import { useRoute } from 'vue-router';
    import UBadge from '~/components/atoms/UBadge.vue';
    import UCard from '~/components/molecules/UCard.vue';
    import { useRuntimeConfig } from '#app';
    import { useAuthFetch } from '~/composables/useAuthFetch';
    import type { AdminDto } from '~/types/dtos/admin.dto';

    definePageMeta({
        layout: 'backoffice',
        middleware: 'admin',
    });

    const route = useRoute();
    const id = ref<string | undefined>(undefined);
    const {
        public: { apiUrl },
    } = useRuntimeConfig();

    const admin = ref<AdminDto | null>(null);
    const pending = ref(false);
    const errorMsg = ref('');

    interface ApiError {
        data?: {
            message?: string;
        };
    }

    async function loadAdmin(adminId: string) {
        pending.value = true;
        errorMsg.value = '';
        try {
            const { data } = await useAuthFetch<{ value: AdminDto }>(`/api/admins/${adminId}`, {
                baseURL: apiUrl,
            });
            admin.value = data.value ?? null;
            if (!admin.value) {
                errorMsg.value = 'Admin non trouvé.';
            }
        } catch (err: unknown) {
            const error = err as ApiError;
            errorMsg.value = error?.data?.message ?? "Erreur lors du chargement de l'administrateur.";
            console.error(err);
        } finally {
            pending.value = false;
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

    const getStatusProps = (verified: boolean | undefined) =>
        verified ? { label: 'Vérifié', color: 'success' } : { label: 'Non vérifié', color: 'error' };
</script>

<template>
    <div class="space-y-6">
        <h1 class="text-2xl font-semibold">Fiche administrateur</h1>

        <div v-if="pending" class="text-gray-600">Chargement…</div>
        <div v-else-if="errorMsg" class="text-red-600">{{ errorMsg }}</div>
        <div v-else-if="!admin" class="text-gray-600">Aucun administrateur trouvé.</div>
        <div v-else class="space-y-6">
            <UCard>
                <template #header>
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-6">
                        <div class="flex items-center gap-6">
                            <img
                                v-if="admin.avatar"
                                :src="admin.avatar"
                                alt="Avatar administrateur"
                                class="w-32 h-32 rounded-full object-cover border shadow"
                            />
                            <div>
                                <div class="text-xl font-semibold">{{ admin.firstName }} {{ admin.lastName }}</div>
                                <div class="text-gray-500">{{ admin.email }}</div>
                            </div>
                        </div>
                        <UBadge :color="getStatusProps(admin.isVerified).color" variant="pill">
                            {{ getStatusProps(admin.isVerified).label }}
                        </UBadge>
                    </div>
                </template>

                <div class="grid md:grid-cols-2 gap-6 mt-4">
                    <div><strong>Prénom :</strong> {{ admin.firstName }}</div>
                    <div><strong>Nom :</strong> {{ admin.lastName }}</div>
                    <div><strong>Email :</strong> {{ admin.email }}</div>
                    <div><strong>Téléphone :</strong> {{ admin.phone || 'Non renseigné' }}</div>
                    <div><strong>Adresse :</strong> {{ admin.address || 'Non renseignée' }}</div>
                    <div>
                        <strong>Date de création :</strong>
                        <span v-if="admin.createdAt">{{ new Date(admin.createdAt).toLocaleDateString() }}</span>
                        <span v-else>Non disponible</span>
                    </div>
                </div>
            </UCard>
        </div>
    </div>
</template>
