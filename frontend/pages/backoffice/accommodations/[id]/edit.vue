<script setup lang="ts">
import { useRoute } from 'vue-router'
import { ref, reactive, watch, computed } from 'vue'
import Input from '~/components/atoms/UInput.vue'
import UInputNumber from '~/components/atoms/UInputNumber.vue'
import Textarea from '~/components/atoms/UTextarea.vue'
import UButton from '~/components/atoms/UButton.vue'
import { useRuntimeConfig } from '#app'
import { useAuthFetch } from '~/composables/useAuthFetch'

interface Theme {
  id: number
  name: string
}

interface Accommodation {
  id: number
  name: string
  description?: string
  address: string
  city?: string
  postalCode?: string
  country?: string
  type?: string
  price: number
  capacity: number
  bedrooms?: number
  bathrooms?: number
  advantage: string[]
  practicalInformations?: string
  latitude?: number
  longitude?: number
  themeId?: number
}

definePageMeta({
  layout: 'backoffice',
  middleware: 'admin',
})

const route = useRoute()
const id = route.params.id as string
const { public: { apiUrl } } = useRuntimeConfig()

const selectedThemeId = ref<number | null>(null)

const { data: themes } = await useAuthFetch<{ themes: Theme[] }>('/api/themes', { baseURL: apiUrl })
const themeList = computed(() => themes.value?.themes ?? [])

const { data: accommodation, refresh, pending } = await useAuthFetch<Accommodation>(`/api/accommodations/${id}`, {
  baseURL: apiUrl,
})

const form = reactive({
  name: '',
  description: '',
  address: '',
  city: '',
  postalCode: '',
  country: '',
  type: '',
  price: 0,
  capacity: 1,
  bedrooms: 0,
  bathrooms: 0,
  practicalInformations: '',
  advantage: '',
  latitude: '',
  longitude: '',
})

watch(accommodation, (newAccommodation) => {
  if (newAccommodation) {
    form.name = newAccommodation.name
    form.description = newAccommodation.description ?? ''
    form.address = newAccommodation.address
    form.city = newAccommodation.city ?? ''
    form.postalCode = newAccommodation.postalCode ?? ''
    form.country = newAccommodation.country ?? ''
    form.type = newAccommodation.type ?? ''
    form.price = newAccommodation.price
    form.capacity = newAccommodation.capacity
    form.bedrooms = newAccommodation.bedrooms ?? 0
    form.bathrooms = newAccommodation.bathrooms ?? 0
    form.practicalInformations = newAccommodation.practicalInformations ?? ''
    form.advantage = newAccommodation.advantage?.join('\n') ?? ''
    form.latitude = newAccommodation.latitude?.toString() ?? ''
    form.longitude = newAccommodation.longitude?.toString() ?? ''
    selectedThemeId.value = newAccommodation.themeId ?? null
  }
}, { immediate: true })

const saving = ref(false)
const success = ref(false)
const errorMsg = ref('')

async function save() {
  saving.value = true
  success.value = false
  errorMsg.value = ''

  try {
    await $fetch(`/api/accommodations/${id}`, {
      method: 'PUT',
      baseURL: apiUrl,
      body: {
        name: form.name,
        description: form.description,
        address: form.address,
        city: form.city,
        postalCode: form.postalCode,
        country: form.country,
        type: form.type,
        price: form.price,
        capacity: form.capacity,
        bedrooms: form.bedrooms,
        bathrooms: form.bathrooms,
        practicalInformations: form.practicalInformations,
        advantage: form.advantage
            .split('\n')
            .map(a => a.trim())
            .filter(Boolean),
        latitude: form.latitude ? parseFloat(form.latitude) : null,
        longitude: form.longitude ? parseFloat(form.longitude) : null,
        themeId: selectedThemeId.value,
      },
    })

    success.value = true
    await refresh()
  } catch (error: any) {
    errorMsg.value = error?.data?.message || 'Erreur lors de l’enregistrement.'
  } finally {
    saving.value = false
  }
}
</script>

<template>
  <div class="space-y-6">
    <h1 class="text-2xl font-semibold">Modifier le logement</h1>

    <form :aria-busy="saving || pending" class="flex flex-col gap-6" @submit.prevent="save" >

      <div class="flex flex-wrap gap-4">
        <Input v-model="form.name" label="Nom" type="text" class="flex-1 min-w-[250px]" required />
        <Input v-model="form.type" label="Type" type="text" class="flex-1 min-w-[250px]" />
      </div>

      <div class="flex flex-wrap gap-4">
        <Input v-model="form.address" label="Adresse" type="text" class="flex-1 min-w-[250px]" required />
        <Input v-model="form.city" label="Ville" type="text" class="flex-1 min-w-[250px]" />
        <Input v-model="form.postalCode" label="Code Postal" type="text" class="flex-1 min-w-[250px]" />
        <Input v-model="form.country" label="Pays" type="text" class="flex-1 min-w-[250px]" />
      </div>

      <div class="flex flex-wrap gap-4">
        <UInputNumber v-model="form.price" label="Prix / nuit (€)" :step="0.01" :min="0" required class="flex-1 min-w-[200px]" />
        <UInputNumber v-model="form.capacity" label="Capacité" :min="1" required class="flex-1 min-w-[200px]" />
        <UInputNumber v-model="form.bedrooms" label="Chambres" :min="0" class="flex-1 min-w-[200px]" />
        <UInputNumber v-model="form.bathrooms" label="Salles de bain" :min="0" class="flex-1 min-w-[200px]" />
      </div>

      <div class="flex flex-wrap gap-4">
        <Input v-model="form.latitude" label="Latitude" type="number" step="any" class="flex-1 min-w-[200px]" />
        <Input v-model="form.longitude" label="Longitude" type="number" step="any" class="flex-1 min-w-[200px]" />
      </div>

      <div class="flex flex-col gap-1.5">
        <label class="text-body-sm">
          Thème <span class="text-brand-tertiary">*</span>
        </label>
        <select
            v-model="selectedThemeId"
            required
            class="border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:outline-none shadow-xs w-full px-3 py-2 text-sm"
        >
          <option disabled>Choisir un thème</option>
          <option v-for="theme in themeList" :key="theme.id" :value="theme.id">
            {{ theme.name }}
          </option>
        </select>
      </div>


      <Textarea v-model="form.description" label="Description" />
      <Textarea v-model="form.practicalInformations" label="Informations pratiques" />
      <Textarea
          v-model="form.advantage"
          label="Avantages (un par ligne)"
          placeholder="Exemple :&#10;Wi-Fi gratuit&#10;Petit-déjeuner offert"
      />


      <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
        <UButton
            :disabled="saving"
            :is-loading="saving"
            size="lg"
            variant="primary"
            type="submit"
        >
          {{ saving ? 'Enregistrement…' : 'Enregistrer' }}
        </UButton>
        <span v-if="success" class="text-green-600 text-sm">Modifications enregistrées</span>
        <span v-if="errorMsg" class="text-red-600 text-sm">{{ errorMsg }}</span>
      </div>
    </form>
  </div>
</template>
