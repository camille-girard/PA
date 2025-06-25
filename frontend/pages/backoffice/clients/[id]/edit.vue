<script setup lang="ts">
import { useRoute, useRouter } from 'vue-router'
import Input from '~/components/atoms/UInput.vue'
import Checkbox from '~/components/atoms/UCheckbox.vue'

definePageMeta({
  layout: 'backoffice',
})

const { public: { apiUrl } } = useRuntimeConfig()

const route = useRoute()
const router = useRouter()

const id = route.params.id

const { data: client, refresh, pending } = await useFetch(`/api/clients/${id}`, {
  baseURL: apiUrl,
  transform: (res) => res.client,
})

const form = reactive({
  firstName: '',
  lastName: '',
  email: '',
  phone: '',
  isVerified: false,
})

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

async function save() {
  saving.value = true
  success.value = false
  errorMsg.value = ''

  try {
    await $fetch(`/api/clients/${id}`, {
      method: 'PUT',
      baseURL: apiUrl,
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
  <div class="max-w-3xl p-6 md:p-10 dark:bg-gray-900 space-y-8">
    <h1 class="text-3xl font-semibold text-gray-800 dark:text-white">Modifier le client</h1>

    <form @submit.prevent="save" class="grid gap-6 md:grid-cols-2" :aria-busy="saving || pending">
      <Input
          v-model="form.firstName"
          label="Prénom"
          name="firstName"
          type="text"
          required
      />
      <Input
          v-model="form.lastName"
          label="Nom"
          name="lastName"
          type="text"
          required
      />
      <Input
          class="md:col-span-2"
          v-model="form.email"
          label="Email"
          name="email"
          type="email"
          required
      />
      <Input
          class="md:col-span-2"
          v-model="form.phone"
          label="Téléphone"
          name="phone"
          type="tel"
      />

      <div class="md:col-span-2 flex flex-col gap-2 sm:flex-row sm:justify-between sm:items-center">
        <UButton
            :disabled="saving"
            :isLoading="saving"
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
