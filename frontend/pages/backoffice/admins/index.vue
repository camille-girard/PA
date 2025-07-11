<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import { NuxtLink } from '#components';
import UTable from '~/components/organisms/UTable.vue';
import UBadge from '~/components/atoms/UBadge.vue';
import TrashIcon from '~/components/atoms/icons/TrashIcon.vue';
import EditIcon from '~/components/atoms/icons/EditIcon.vue';
import ConfirmPopover from '~/components/ConfirmPopover.vue';
import EyeView from '~/components/atoms/icons/EyeView.vue';
import { useRuntimeConfig } from '#app';
import { useAuthFetch } from '~/composables/useAuthFetch';
import { useToast } from '~/composables/useToast';
import { useAuthStore } from '~/stores/auth';
import type { AdminDto } from '~/types/dtos/admin.dto';

interface AdminRow {
  id: number;
  admin: string;
  phone: string;
  email: string;
  status: string;
  _original: AdminDto;
}

definePageMeta({
  layout: 'backoffice',
  middleware: 'admin',
});

const toast = useToast();
const authStore = useAuthStore();
const myId = computed(() => authStore.user?.id);

const { public: { apiUrl } } = useRuntimeConfig();

const adminData = ref<AdminDto[]>([]);
const pending = ref(false);

const columns = [
  { key: 'admin', label: 'Admin', sortable: true },
  { key: 'phone', label: 'Téléphone' },
  { key: 'email', label: 'Email' },
  { key: 'status', label: 'Vérification' },
  { key: 'actions', label: '' },
];

const admins = computed(() => adminData.value || []);

const adminsData = computed<AdminRow[]>(() =>
    Array.isArray(admins.value)
        ? admins.value.map((admin: AdminDto) => ({
          id: admin.id,
          admin: `${admin.firstName} ${admin.lastName}`,
          phone: admin.phone || 'Non renseigné',
          email: admin.email,
          status: admin.isVerified ? 'verified' : 'unverified',
          _original: admin,
        }))
        : []
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

async function loadAdmins() {
  pending.value = true;
  try {
    const { data } = await useAuthFetch('/api/admins', { baseURL: apiUrl });

    if (data.value) {
      if (Array.isArray(data.value.value)) {
        adminData.value = data.value;
      } else if (Array.isArray(data.value)) {
        adminData.value = data.value;
      } else {
        adminData.value = [];
        toast.error('Erreur', 'Réponse inattendue du serveur.');
      }
    } else {
      adminData.value = [];
      toast.error('Erreur', 'Aucune donnée reçue.');
    }
  } catch (error: unknown) {
    toast.error('Erreur', error?.data?.message || 'Erreur lors du chargement des administrateurs.');
    console.error(error);
  } finally {
    pending.value = false;
  }
}


async function refreshAdmins() {
  await loadAdmins();
}

async function deleteAdmin(id: string) {
  pending.value = true;
  try {
    await useAuthFetch(`/api/admins/${id}`, {
      method: 'DELETE',
      baseURL: apiUrl,
    });
    await refreshAdmins();
    toast.success('Suppression réussie','Administrateur supprimé avec succès.');
  } catch (error: unknown) {
    toast.error('Erreur', error?.data?.message || 'Erreur lors de la suppression.');
    console.error(error);
  } finally {
    pending.value = false;
  }
}

onMounted(() => {
  loadAdmins();
});

</script>

<template>
  <div class="space-y-6">
    <p class="text-2xl font-semibold">Administrateurs</p>

    <div v-if="pending" class="text-gray-600">Chargement…</div>

    <div v-else>
      <UTable :columns="columns" :data="adminsData">

        <template #cell-status="{ value }: { value: string }">
          <UBadge size="sm" variant="pill" :color="getStatusProps(value).color" class="w-fit">
            {{ getStatusProps(value).label }}
          </UBadge>
        </template>

        <template #cell-actions="{ row }: { row: AdminRow }">
          <div v-if="row.id !== myId" class="flex items-center gap-4">
            <NuxtLink :to="`/backoffice/admins/${row.id}`" class="text-brand-600 hover:text-brand-700">
              <EyeView class="w-6 h-6" />
            </NuxtLink>

            <NuxtLink :to="`/backoffice/admins/${row.id}/edit`" class="text-blue-500 hover:text-blue-800">
              <EditIcon class="w-6 h-6" />
            </NuxtLink>

            <ConfirmPopover :item-name="row.admin" @confirm="deleteAdmin(row.id)">
              <template #trigger>
                <button class="text-red-500 hover:text-red-700">
                  <TrashIcon class="w-6 h-6" />
                </button>
              </template>
            </ConfirmPopover>
          </div>
          <div v-else class="text-gray-400">
            <span>Compte actuel</span>
          </div>
        </template>

      </UTable>
    </div>
  </div>
</template>

