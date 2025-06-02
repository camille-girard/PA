<script setup lang="ts">
import { useRoute, useRouter } from 'vue-router'
import Input from '~/components/atoms/UInput.vue'
import Checkbox from '~/components/atoms/UCheckbox.vue'

definePageMeta({
  layout: 'backoffice',
})

const route = useRoute()
const router = useRouter()

const id = route.params.id

// Données du client
const { data: client, refresh, pending } = await useFetch(`/api/clients/${id}`, {
  baseURL: 'http://localhost',
  transform: (res) => res.client,
})

// Formulaire réactif
const form = reactive({
  firstName: '',
  lastName: '',
  email: '',
  phone: '',
  isVerified: false,
})

// Pré-remplissage dès que les données arrivent
watchEffect(() => {
  if (client.value) {
    form.firstName = client.value.firstName
    form.lastName = client.value.lastName
    form.email = client.value.email
    form.phone = client.value.phone
    form.isVerified = client.value.isVerified
  }
})

const saving = ref(false)
const success = ref(false)
const errorMsg = ref('')

// Enregistrement
async function save() {
  saving.value = true
  success.value = false
  errorMsg.value = ''

  try {
    await $fetch(`/api/clients/${id}`, {
      method: 'PUT',
      baseURL: 'http://localhost',
      body: {
        firstName: form.firstName,
        lastName: form.lastName,
        email: form.email,
        phone: form.phone,
        isVerified: form.isVerified,
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
  <div class="space-y-6 max-w-xl">
    <h1 class="text-2xl font-bold">Modifier le client</h1>

    <form @submit.prevent="save" class="space-y-4">
      <Input v-model="form.firstName" label="Prénom" name="firstName" type="text" required />
      <Input v-model="form.lastName" label="Nom" name="lastName" type="text" required />
      <Input v-model="form.email" label="Email" name="email" type="email" required />
      <Input v-model="form.phone" label="Téléphone" name="phone" type="tel" />
      <Checkbox v-model="form.isVerified" name="isVerified" label="Compte vérifié" />

      <div class="flex justify-between items-center">
        <button
            type="submit"
            class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
            :disabled="saving"
        >
          {{ saving ? 'Enregistrement...' : 'Enregistrer' }}
        </button>

        <span v-if="success" class="text-green-600 text-sm">✅ Modifications enregistrées</span>
        <span v-if="errorMsg" class="text-red-600 text-sm">❌ {{ errorMsg }}</span>
      </div>
    </form>
  </div>
</template>
