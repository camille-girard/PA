<script setup lang="ts">
    import { ref, computed, onMounted } from 'vue';
    import UTable from '~/components/organisms/UTable.vue';
    import UBadge from '~/components/atoms/UBadge.vue';
    import TrashIcon from '~/components/atoms/icons/TrashIcon.vue';
    import EditIcon from '~/components/atoms/icons/EditIcon.vue';
    import ConfirmPopover from '~/components/ConfirmPopover.vue';
    import EyeView from '~/components/atoms/icons/EyeView.vue';
    import { useRuntimeConfig } from '#app';
    import { useAuthFetch } from '~/composables/useAuthFetch';
    import type { Client } from '~/types/client';

    definePageMeta({
        layout: 'backoffice',
        middleware: 'admin',
    });

    const {
        public: { apiUrl },
    } = useRuntimeConfig();

    const clientData = ref<Client[]>([]);
    const pending = ref(false);
    const errorMsg = ref('');
    const successMsg = ref('');

    const columns = [
        { key: 'client', label: 'Client', sortable: true },
        { key: 'phone', label: 'Téléphone' },
        { key: 'email', label: 'Email' },
        { key: 'bookingCount', label: 'Réservations' },
        { key: 'status', label: 'Vérification' },
        { key: 'actions', label: '' },
    ];

    const clients = computed(() => clientData.value || []);

    const clientsData = computed(() =>
        clients.value.map((client) => ({
            id: client.id,
            client: `${client.firstName} ${client.lastName}`,
            phone: client.phone || 'Non renseigné',
            email: client.email,
            bookingCount: client.bookingCount ?? 0,
            status: client.isVerified ? 'verified' : 'unverified',
            _original: client,
        }))
    );

    function getStatusProps(status: string) {
        switch (status.toLowerCase()) {
            case 'verified':
                return { label: 'Vérifié', color: 'success' };
            case 'unverified':
                return { label: 'Non vérifié', color: 'error' };
            default:
                return { label: status, color: 'brand' };
        }
    }

    async function loadClients() {
        pending.value = true;
        errorMsg.value = '';
        try {
            const { data } = await useAuthFetch('/api/clients', { baseURL: apiUrl });
            clientData.value = data.value || [];
        } catch (error: unknown) {
            errorMsg.value = error?.data?.message || 'Erreur lors du chargement des clients.';
        } finally {
            pending.value = false;
        }
    }

    async function refreshClients() {
        await loadClients();
    }

    async function deleteClient(id: string) {
        successMsg.value = '';
        errorMsg.value = '';
        pending.value = true;
        try {
            await $fetch(`/api/clients/${id}`, {
                method: 'DELETE',
                baseURL: apiUrl,
            });
            await refreshClients();
            successMsg.value = 'Client supprimé avec succès.';
        } catch (error: unknown) {
            if (error?.data?.message) {
                errorMsg.value = error.data.message;
            } else {
                errorMsg.value = 'Erreur lors de la suppression.';
            }
            console.error(error);
        } finally {
            pending.value = false;
        }
    }

    onMounted(() => {
        loadClients();
    });
</script>

<template>
    <div class="space-y-6">
        <p class="text-2xl font-semibold">Clients</p>

        <div v-if="pending" class="text-gray-600">Chargement…</div>
        <div v-else>
            <div v-if="successMsg" class="text-green-600 text-sm">{{ successMsg }}</div>
            <div v-if="errorMsg" class="text-red-600 text-sm">{{ errorMsg }}</div>

            <UTable :columns="columns" :data="clientsData">
                <template #cell-status="{ value }">
                    <UBadge size="sm" variant="pill" :color="getStatusProps(value).color" class="w-fit">
                        {{ getStatusProps(value).label }}
                    </UBadge>
                </template>

                <template #cell-actions="{ row }">
                    <div class="flex items-center gap-4">
                        <NuxtLink :to="`/backoffice/clients/${row.id}`" class="text-brand-600 hover:text-brand-700">
                            <EyeView class="w-6 h-6" />
                        </NuxtLink>

                        <NuxtLink :to="`/backoffice/clients/${row.id}/edit`" class="text-blue-500 hover:text-blue-800">
                            <EditIcon class="w-6 h-6" />
                        </NuxtLink>

                        <ConfirmPopover :item-name="row.client" @confirm="deleteClient(row.id)">
                            <template #trigger>
                                <button class="text-red-500 hover:text-red-700">
                                    <TrashIcon class="w-6 h-6" />
                                </button>
                            </template>
                        </ConfirmPopover>
                    </div>
                </template>
            </UTable>
        </div>
    </div>
</template>
