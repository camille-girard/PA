<script setup lang="ts">
  import { ref, reactive, onMounted } from 'vue'
  import { useRuntimeConfig } from '#app'
  import { useAuthFetch } from '~/composables/useAuthFetch'
  import UCard from '~/components/molecules/UCard.vue'
  import UInput from '~/components/atoms/UInput.vue'
  import UTextarea from '~/components/atoms/UTextarea.vue'
  import UButton from '~/components/atoms/UButton.vue'
  import ConfirmPopover from '~/components/ConfirmPopover.vue'
  import TrashIcon from '~/components/atoms/icons/TrashIcon.vue'
  import EditIcon from '~/components/atoms/icons/EditIcon.vue'
  import type { ThemeDto } from '~/types/dtos/theme.dto';

  definePageMeta({
    layout: 'backoffice',
    middleware: 'admin',
  });

    const {
        public: { apiUrl },
    } = useRuntimeConfig();

    const themes = ref<ThemeDto[]>([]);
    const pending = ref(false);
    const isSaving = ref(false);
    const editingTheme = ref<number | null>(null);
    const successMsg = ref('');
    const errorMsg = ref('');

    const originalThemeData = ref<any>({})
    const editingThemeData = ref<any>({})

    const newTheme = reactive({
        name: '',
        description: '',
    });

    async function loadThemes() {
      pending.value = true;
      errorMsg.value = '';
      try {
        const { data } = await useAuthFetch('/api/themes', {
          baseURL: apiUrl,
          transform: (res) => res.themes,
        });

        themes.value = (data.value || []).filter(theme =>
            theme && theme.id && theme.name && theme.description
        )
      } catch (error: unknown) {
        errorMsg.value = error?.data?.message || 'Erreur lors du chargement des thèmes.'
        console.error(error);
      } finally {
        pending.value = false;
      }
    }

    async function refreshThemes() {
        await loadThemes();
    }

async function saveNewTheme() {
  if (isSaving.value) return

  isSaving.value = true
  successMsg.value = ''
  errorMsg.value = ''

  try {
    if (!newTheme.name.trim() || !newTheme.description.trim()) {
      errorMsg.value = 'Le nom et la description sont requis.'
      return
    }

    const response = await useAuthFetch('/api/themes', {
      method: 'POST',
      baseURL: apiUrl,
      body: {
        name: newTheme.name.trim(),
        description: newTheme.description.trim()
      },
    })

    if (response.error.value) {
      errorMsg.value = response.error.value.data?.message || 'Erreur lors de l\'ajout du thème.'
      return
    }

    Object.assign(newTheme, { name: '', description: '' })

    await refreshThemes()
    successMsg.value = 'Thème ajouté avec succès.'

  } catch (error: any) {
    errorMsg.value = error?.data?.message || 'Erreur lors de l\'ajout du thème.'
    console.error('Erreur lors de l\'ajout:', error)
  } finally {
    isSaving.value = false;
  }
}

function startEdit(theme: any) {
  editingTheme.value = theme.id
  originalThemeData.value = {
    name: theme.name,
    description: theme.description
  }
  editingThemeData.value = {
    name: theme.name,
    description: theme.description
  }
}

async function updateTheme(themeId: number) {
  successMsg.value = ''
  errorMsg.value = ''
  try {
    const response = await useAuthFetch(`/api/themes/${themeId}`, {
      method: 'PUT',
      baseURL: apiUrl,
      body: {
        name: editingThemeData.value.name,
        description: editingThemeData.value.description,
      },
    })

    if (response.error.value) {
      errorMsg.value = response.error.value.data?.message || 'Erreur lors de la mise à jour.'
      return
    }

    editingTheme.value = null
    editingThemeData.value = {}
    originalThemeData.value = {}

    await refreshThemes()
    successMsg.value = 'Thème mis à jour.'
  } catch (error: any) {
    errorMsg.value = error?.data?.message || 'Erreur lors de la mise à jour.'
    console.error('Erreur lors de la mise à jour:', error)
  }
}

function cancelEdit() {
  editingTheme.value = null
  editingThemeData.value = {}
  originalThemeData.value = {}
}

async function deleteTheme(id: number) {
  successMsg.value = ''
  errorMsg.value = ''

  try {
    const response = await useAuthFetch(`/api/themes/${id}`, {
      method: 'DELETE',
      baseURL: apiUrl,
    })

    if (response.error.value) {
      errorMsg.value = response.error.value.data?.message || 'Erreur lors de la suppression du thème.'
      return
    }

    themes.value = themes.value.filter(theme => theme.id !== id)
    successMsg.value = 'Thème supprimé avec succès.'

  } catch (error: any) {
    errorMsg.value = error?.data?.message || 'Erreur lors de la suppression du thème.'
    console.error(error)
  }
}

onMounted(() => {
  loadThemes()
})
</script>

<template>
  <div class="space-y-6">
    <h1 class="text-2xl font-semibold">Gestion des thèmes</h1>
    <div v-if="pending" class="text-gray-600">Chargement…</div>
    <div v-else>
      <div v-if="successMsg" class="text-green-600 text-sm mb-4">{{ successMsg }}</div>
      <div v-if="errorMsg" class="text-red-600 text-sm mb-4">{{ errorMsg }}</div>
      <UCard>
        <template #header>
          <span class="text-lg font-medium">Ajouter un nouveau thème</span>
        </template>
        <div class="flex flex-col gap-4">
          <UInput v-model="newTheme.name" label="Nom" type="text" required />
          <UTextarea v-model="newTheme.description" label="Description" required />
          <UButton
              :is-loading="isSaving"
              :disabled="isSaving || !newTheme.name.trim() || !newTheme.description.trim()"
              class="w-fit justify-self-start"
              @click="saveNewTheme"
          >
            {{ isSaving ? 'Ajout en cours...' : 'Ajouter' }}
          </UButton>
        </div>
      </UCard>
      <div>
        <h2 class="text-lg font-semibold my-4">Tous les thèmes</h2>
        <div class="grid md:grid-cols-3 lg:grid-cols-4 gap-6">
          <UCard v-for="theme in themes" :key="theme.id">
            <template #header>
              <div class="flex justify-between items-center">
                <div class="font-semibold">{{ theme.name }}</div>
                <div class="flex items-center gap-2">
                  <button @click="startEdit(theme)">
                    <EditIcon class="w-5 h-5 text-blue-500 hover:text-blue-700" />
                  </button>
                  <ConfirmPopover :item-name="theme.name" @confirm="deleteTheme(theme.id)">
                    <template #trigger>
                      <button class="text-red-500 hover:text-red-700">
                        <TrashIcon class="w-5 h-5" />
                      </button>
                    </template>
                  </ConfirmPopover>
                </div>
              </div>
            </template>
            <template v-if="editingTheme === theme.id">
              <div class="space-y-3">
                <UInput v-model="editingThemeData.name" label="Nom" type="text" />
                <UTextarea v-model="editingThemeData.description" label="Description" />
                <div class="flex gap-2">
                  <UButton @click="updateTheme(theme.id)">Enregistrer</UButton>
                  <UButton variant="ghost" @click="cancelEdit()">Annuler</UButton>
                </div>
              </div>
            </template>
            <template v-else>
              <div class="space-y-2">
                <p><strong>Nom :</strong> {{ theme.name }}</p>
                <p><strong>Description :</strong> {{ theme.description }}</p>
              </div>
            </template>
          </UCard>
        </div>
      </div>
    </div>
  </div>
</template>