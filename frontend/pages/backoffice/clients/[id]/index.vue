<script setup lang="ts">
import { ref, computed, watch } from 'vue';
import { useRoute } from 'vue-router';
import UBadge from '~/components/atoms/UBadge.vue';
import UCard from '~/components/molecules/UCard.vue';
import UTable from '~/components/organisms/UTable.vue';
import { useRuntimeConfig } from '#app';
import { useAuthFetch } from '~/composables/useAuthFetch';
import ULink from '~/components/atoms/ULink.vue';
import ArrowLeftIcon from '~/components/atoms/icons/ArrowLeftIcon.vue';

definePageMeta({
  layout: 'backoffice',
  middleware: 'admin',
});

const route = useRoute();
const id = ref<string | undefined>(undefined);
const {
  public: { apiUrl },
} = useRuntimeConfig();

interface Booking {
  id: number;
  accommodation?: {
    name?: string;
  };
  startDate: string;
  endDate: string;
  status: string;
  totalPrice: number;
}

interface Client {
  id: number;
  firstName?: string;
  lastName?: string;
  email?: string;
  phone?: string;
  address?: string;
  avatar?: string;
  roles?: string[];
  createdAt: string;
  isVerified: boolean;
  preferences?: string[];
  bookings?: Booking[];
}

interface ApiError {
  data?: {
    message?: string;
  };
}

const client = ref<Client | null>(null);
const pending = ref(false);
const errorMsg = ref('');

function isApiError(error: unknown): error is ApiError {
  return typeof error === 'object' && error !== null && 'data' in error;
}

async function loadClient(clientId: string) {
  pending.value = true;
  errorMsg.value = '';
  try {
    const { data } = await useAuthFetch<{ client: Client }>(`/api/clients/${clientId}`, {
      baseURL: apiUrl,
    });
    client.value = data.value ?? null;
  } catch (error) {
    if (isApiError(error)) {
      errorMsg.value = error.data?.message || 'Erreur lors du chargement du client.';
    } else {
      errorMsg.value = 'Erreur lors du chargement du client.';
    }
    console.error(error);
  } finally {
    pending.value = false;
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

const getStatusProps = (verified: boolean | undefined) =>
    verified ? { label: 'Vérifié', color: 'success' } : { label: 'Non vérifié', color: 'error' };

const bookings = computed(
    () =>
        client.value?.bookings?.map((b: Booking) => ({
          id: b.id,
          accommodation: b.accommodation?.name ?? '—',
          startDate: new Date(b.startDate).toLocaleDateString(),
          endDate: new Date(b.endDate).toLocaleDateString(),
          status: b.status,
          totalPrice: `${b.totalPrice?.toFixed(2)} €`,
        })) ?? []
);
</script>

<template>
  <div class="space-y-6">
    <ULink to="/backoffice/clients" size="lg" class="flex flex-row gap-2">
      <ArrowLeftIcon /> Retour à la liste
    </ULink>

    <h1 class="text-2xl font-semibold">Fiche client</h1>

    <div v-if="pending" class="text-gray-600">Chargement…</div>
    <div v-else-if="errorMsg" class="text-red-600">{{ errorMsg }}</div>
    <div v-else-if="!client" class="text-gray-600">Aucun client trouvé.</div>
    <div v-else class="space-y-6">
      <UCard>
        <template #header>
          <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-6">
            <div class="flex items-center gap-6">
              <img
                  v-if="client.avatar"
                  :src="client.avatar"
                  alt="Avatar du client"
                  class="w-24 h-24 rounded-full object-cover border shadow"
              />
              <div>
                <div class="text-xl font-semibold">{{ client.firstName }} {{ client.lastName }}</div>
                <div class="text-gray-500">{{ client.email }}</div>
              </div>
            </div>
            <UBadge :color="getStatusProps(client.isVerified).color" variant="pill">
              {{ getStatusProps(client.isVerified).label }}
            </UBadge>
          </div>
        </template>

        <div class="grid md:grid-cols-2 gap-6 mt-4">
          <div><strong>Email :</strong> {{ client.email }}</div>
          <div><strong>Téléphone :</strong> {{ client.phone || 'Non renseigné' }}</div>
          <div><strong>Adresse :</strong> {{ client.address || 'Non renseignée' }}</div>
          <div><strong>Préférences :</strong> {{ client.preferences?.join(', ') || '—' }}</div>
          <div>
            <strong>Date de création :</strong>
            {{ new Date(client.createdAt).toLocaleDateString() }}
          </div>
        </div>
      </UCard>

      <div>
        <h2 class="text-lg font-semibold mb-4">Réservations</h2>
        <UTable
            :columns="[
            { key: 'accommodation', label: 'Hébergement' },
            { key: 'startDate', label: 'Début' },
            { key: 'endDate', label: 'Fin' },
            { key: 'status', label: 'Statut' },
            { key: 'totalPrice', label: 'Prix total' },
          ]"
            :data="bookings"
            :loading="pending"
        />
      </div>
    </div>
  </div>
</template>
