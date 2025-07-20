<script setup lang="ts">
    import { ref, computed, onMounted } from 'vue';
    import { useRouter } from 'vue-router';
    import { useAuthFetch } from '~/composables/useAuthFetch';
    import UCard from '~/components/molecules/UCard.vue';
    import UBadge from '~/components/atoms/UBadge.vue';
    import UButton from '~/components/atoms/UButton.vue';
    import { useRuntimeConfig } from '#app';
    import type { Ticket } from '~/types/ticket';
    import UPagination from '~/components/UPagination.vue';

    definePageMeta({
        layout: 'backoffice',
        middleware: 'admin',
    });

    const router = useRouter();
    const {
        public: { apiUrl },
    } = useRuntimeConfig();

    const selectedStatus = ref<'ALL' | 'OPEN' | 'IN_PROGRESS' | 'CLOSED'>('ALL');
    const ticketsData = ref<Ticket[]>([]);
    const pending = ref(true);
    const error = ref<Error | null>(null);

    async function fetchTickets() {
        pending.value = true;
        error.value = null;
        try {
            const { data } = await useAuthFetch('/api/admin/tickets', {
                baseURL: apiUrl,
            });
            ticketsData.value = data.value?.tickets ?? [];
        } catch (e) {
            error.value = e;
        } finally {
            pending.value = false;
        }
    }

    onMounted(() => {
        fetchTickets();
    });

    const filteredTickets = computed(() => {
        if (!ticketsData.value) return [];
        if (selectedStatus.value === 'ALL') return ticketsData.value;
        return ticketsData.value.filter((t) => t.status === selectedStatus.value);
    });

    const {
        paginatedItems: paginatedTickets,
        meta,
        goToPage,
        nextPage,
        previousPage,
        firstPage,
        lastPage,
        setItemsPerPage,
    } = usePagination(filteredTickets, { itemsPerPage: 12 });

    function openTicket(ticket) {
        router.push(`/backoffice/tickets/${ticket.id}`);
    }

    function statusColor(status) {
        switch (status) {
            case 'OPEN':
                return 'error';
            case 'IN_PROGRESS':
                return 'warning';
            case 'CLOSED':
                return 'success';
            default:
                return 'gray';
        }
    }

    function statusLabel(status: string) {
        switch (status) {
            case 'OPEN':
                return 'Ouvert';
            case 'IN_PROGRESS':
                return 'En cours';
            case 'CLOSED':
                return 'Fermé';
            default:
                return status;
        }
    }
</script>

<template>
    <div class="space-y-8">
        <h2 class="text-2xl font-semibold flex items-center gap-2">
            Tickets de Support
            <UBadge variant="pill" color="brand" size="md">
                {{ ticketsData.length }}
            </UBadge>
        </h2>

        <div class="flex gap-3">
            <UButton :variant="selectedStatus === 'ALL' ? 'primary' : 'ghost'" @click="selectedStatus = 'ALL'"
                >Tous</UButton
            >
            <UButton :variant="selectedStatus === 'OPEN' ? 'primary' : 'ghost'" @click="selectedStatus = 'OPEN'"
                >Ouvert</UButton
            >
            <UButton
                :variant="selectedStatus === 'IN_PROGRESS' ? 'primary' : 'ghost'"
                @click="selectedStatus = 'IN_PROGRESS'"
                >En cours</UButton
            >
            <UButton :variant="selectedStatus === 'CLOSED' ? 'primary' : 'ghost'" @click="selectedStatus = 'CLOSED'"
                >Fermé</UButton
            >
        </div>

        <div v-if="pending" class="text-gray-500">Chargement des tickets...</div>
        <div v-else-if="error" class="text-red-600">Erreur lors du chargement des tickets</div>
        <div v-else>
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                <UCard
                    v-for="ticket in paginatedTickets"
                    :key="ticket.id"
                    class="cursor-pointer hover:shadow-md transition"
                    @click="openTicket(ticket)"
                >
                    <template #header>
                        <div class="flex justify-between items-center">
                            <div class="font-semibold">{{ ticket.title }}</div>
                            <UBadge :color="statusColor(ticket.status)" class="text-center">{{ statusLabel(ticket.status) }}</UBadge>
                        </div>
                    </template>
                    <div class="text-sm text-gray-600">
                        <p>
                            <strong>Propriétaire :</strong> {{ ticket.owner?.firstName }} {{ ticket.owner?.lastName }}
                        </p>
                        <p><strong>Créé le :</strong> {{ new Date(ticket.createdAt).toLocaleDateString() }}</p>
                    </div>
                </UCard>
            </div>

            <UPagination
                :meta="meta"
                @go-to-page="goToPage"
                @next-page="nextPage"
                @previous-page="previousPage"
                @first-page="firstPage"
                @last-page="lastPage"
                @set-items-per-page="setItemsPerPage"
            />
        </div>
    </div>
</template>
