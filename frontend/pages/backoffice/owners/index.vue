<script setup lang="ts">
    import UTable from '~/components/organisms/UTable.vue';
    import UBadge from '~/components/atoms/UBadge.vue';
    import FullStarIcon from '~/components/atoms/icons/FullStarIcon.vue';
    import HalfStarIcon from '~/components/atoms/icons/HalfStarIcon.vue';
    import EmptyStarIcon from '~/components/atoms/icons/EmptyStarIcon.vue';
    import TrashIcon from '~/components/atoms/icons/TrashIcon.vue';
    import EditIcon from '~/components/atoms/icons/EditIcon.vue';
    import ConfirmPopover from '~/components/ConfirmPopover.vue';
    import EyeView from '~/components/atoms/icons/EyeView.vue';
    import { useRuntimeConfig } from '#app';
    import { useAuthFetch } from '~/composables/useAuthFetch';
    import type { OwnerDto } from '~/types/dtos/owner.dto';
    import { useToast } from '~/composables/useToast';
    import type { ApiError } from '~/types/apiError';

    definePageMeta({
        layout: 'backoffice',
        middleware: 'admin',
    });

    const {
        public: { apiUrl },
    } = useRuntimeConfig();

    const ownerData = ref<OwnerDto[]>([]);

    onMounted(async () => {
        const { data } = await useAuthFetch('/api/owners', { baseURL: apiUrl });
        ownerData.value = data.value;
    });

    const owners = computed(() => ownerData.value || []);

    const toast = useToast();

    async function refreshOwners() {
        const { data } = await useAuthFetch('/api/owners', { baseURL: apiUrl });
        ownerData.value = data.value;
    }

    async function deleteOwner(id: string) {
        try {
            await $fetch(`/api/owners/${id}`, {
                method: 'DELETE',
                baseURL: apiUrl,
            });
            await refreshOwners();
            toast.success('Succès', 'Hôte supprimé avec succès.');
        } catch (error: unknown) {
            const err = error as ApiError;
            console.error(err);
            toast.error('Erreur', err?.data?.message || err?.message || 'Erreur lors de la suppression.');
        }
    }

    const columns = [
        { key: 'owner', label: 'Hôte', sortable: true },
        { key: 'phone', label: 'Téléphone' },
        { key: 'email', label: 'Email' },
        { key: 'accommodationCount', label: 'Logements' },
        { key: 'rating', label: 'Note' },
        { key: 'status', label: 'Status' },
        { key: 'actions', label: '' },
    ];

    const ownersData = computed(() =>
        owners.value.map((owner) => ({
            id: owner.id,
            owner: `${owner.firstName} ${owner.lastName}`,
            phone: owner.phone || 'Non renseigné',
            email: owner.email,
            accommodationCount: owner.accommodationCount ?? 0,
            rating: owner.rating ?? 0,
            status: owner.isVerified ? 'active' : 'inactive',
            _original: owner,
        }))
    );

    function getStatusProps(status: string) {
        switch (status.toLowerCase()) {
            case 'active':
                return { label: 'Actif', color: 'success' };
            case 'inactive':
                return { label: 'Inactif', color: 'error' };
            case 'pending':
            case 'en attente':
                return { label: 'En attente', color: 'warning' };
            default:
                return { label: status, color: 'brand' };
        }
    }
</script>

<template>
    <div class="space-y-6">
        <h2 class="text-2xl font-semibold flex items-center gap-2">
            Hôtes
            <UBadge variant="pill" color="brand" size="md">
                {{ owners.length }}
            </UBadge>
        </h2>

        <UTable :columns="columns" :data="ownersData">
            <template #cell-status="{ value }">
                <UBadge size="sm" variant="pill" :color="getStatusProps(value).color" class="w-fit">
                    {{ getStatusProps(value).label }}
                </UBadge>
            </template>

            <template #cell-rating="{ value }">
                <div class="flex items-center gap-0.5">
                    <component
                        :is="value >= i ? FullStarIcon : value >= i - 0.5 ? HalfStarIcon : EmptyStarIcon"
                        v-for="i in 5"
                        :key="i"
                    />
                </div>
            </template>

            <template #cell-actions="{ row }">
                <div class="flex items-center gap-4">
                    <NuxtLink :to="`/backoffice/owners/${row.id}`" class="text-brand-600 hover:text-brand-700">
                        <EyeView class="w-6 h-6" />
                    </NuxtLink>

                    <NuxtLink :to="`/backoffice/owners/${row.id}/edit`" class="text-blue-500 hover:text-blue-800">
                        <EditIcon class="w-6 h-6" />
                    </NuxtLink>

                    <ConfirmPopover :item-name="row.owner" @confirm="deleteOwner(row.id)">
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
</template>
